<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Modulo;
use App\Models\ModuloDetalle;
use App\Models\Paquete;
use App\Models\ServicioNutrinext;
use App\Traits\MaintainsFilters;

class ServicioNutrinextController extends BaseController
{
    use MaintainsFilters;

    private function esSuperAdmin(): bool
    {
        $usuario = session()->get('usuario');

        return isset($usuario['poder']) && (int) $usuario['poder'] === 3;
    }

    private function menuData(): array
    {
        $menuTotal = [];
        $modulo    = new ModuloDetalle();
        $menu      = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($menu as $entity) {
            $menuTotal[] = [
                'menu'    => $entity,
                'submenu' => $modulo->getSubMenu($entity['id']),
            ];
        }

        return ['data' => $menuTotal];
    }

    private function denyIfNotSa()
    {
        if (! $this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No tienes permisos para acceder a Servicios NutriNext');
        }

        return null;
    }

    public function lista()
    {
        if ($deny = $this->denyIfNotSa()) {
            return $deny;
        }

        $model = new ServicioNutrinext();
        $items = $model->getCatalogo('A');
        $porCategoria = [];
        foreach ($items as $item) {
            $porCategoria[$item->categoria][] = $item;
        }

        return view('Modulos/servicio_nutrinext/lista', array_merge($this->menuData(), [
            'por_categoria' => $porCategoria,
            'categorias'  => ServicioNutrinext::categorias(),
        ]));
    }

    public function getServicios()
    {
        if (! session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }
        if (! $this->esSuperAdmin()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(403);
        }

        $model     = new ServicioNutrinext();
        $draw      = (int) ($this->request->getGet('draw') ?: 1);
        $categoria = $this->request->getGet('categoria');
        $estado    = $this->request->getGet('estado');
        $busqueda  = trim((string) $this->request->getGet('busqueda'));

        $builder = $model;
        if ($categoria !== null && $categoria !== '') {
            $builder = $builder->where('categoria', $categoria);
        }
        if ($estado !== null && $estado !== '') {
            $builder = $builder->where('estado', $estado);
        }
        if ($busqueda !== '') {
            $builder = $builder->groupStart()
                ->like('nombre', $busqueda)
                ->orLike('codigo', $busqueda)
                ->orLike('descripcion_corta', $busqueda)
                ->orLike('etiquetas', $busqueda)
                ->groupEnd();
        }

        $rows = $builder->orderBy('orden', 'ASC')->orderBy('nombre', 'ASC')->findAll();
        $cats = ServicioNutrinext::categorias();

        $data = [];
        foreach ($rows as $r) {
            $catLabel = $cats[$r->categoria] ?? $r->categoria;
            $badges   = '';
            if ($r->visible_web === 'S') {
                $badges .= '<span class="badge bg-info me-1">Web</span>';
            }
            if ($r->visible_catalogo === 'S') {
                $badges .= '<span class="badge bg-primary me-1">Catalogo</span>';
            }
            if ($r->destacado === 'S') {
                $badges .= '<span class="badge bg-warning text-dark me-1">Destacado</span>';
            }
            if ($r->requiere_configuracion === 'S') {
                $badges .= '<span class="badge bg-secondary me-1">Config</span>';
            }

            $icono = '<i class="' . esc($r->icono ?: 'fas fa-circle') . '" style="color:' . esc($r->color ?: '#7bc143') . '"></i>';

            $linkModulo = $r->ruta_dashboard
                ? '<a href="' . base_url(ltrim($r->ruta_dashboard, '/')) . '" class="btn btn-sm btn-outline-success" target="_blank" title="Ir"><i class="fas fa-external-link-alt"></i></a>'
                : '';

            $data[] = [
                $icono . ' <strong>' . esc($r->nombre) . '</strong><br><small class="text-muted">' . esc($r->codigo) . '</small>',
                esc($catLabel),
                esc(mb_strimwidth($r->descripcion_corta, 0, 80, '...')),
                $badges ?: '-',
                $r->estado === 'A' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>',
                (int) $r->orden,
                '<a href="' . base_url('dashboard/servicio-nutrinext/editar/' . $r->id) . '" class="btn btn-sm btn-primary me-1"><i class="fas fa-edit"></i></a>'
                . $linkModulo
                . ' <button type="button" class="btn btn-sm btn-danger btn-eliminar-sn" data-id="' . $r->id . '"><i class="fas fa-trash"></i></button>',
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $model->countAll(),
            'recordsFiltered' => count($rows),
            'data'            => $data,
        ]);
    }

    public function registro()
    {
        if ($deny = $this->denyIfNotSa()) {
            return $deny;
        }

        return view('Modulos/servicio_nutrinext/registro', array_merge($this->menuData(), $this->formExtras()));
    }

    public function editar($id = null)
    {
        if ($deny = $this->denyIfNotSa()) {
            return $deny;
        }

        $model  = new ServicioNutrinext();
        $item   = $model->find($id);
        if (! $item) {
            return redirect()->to(base_url('dashboard/servicio-nutrinext/lista'))
                ->with('error', 'Servicio no encontrado');
        }

        $extras = $this->formExtras();
        $extras['item'] = $item;
        $extras['paquetes_seleccionados'] = $model->getPaqueteIds((int) $id);

        return view('Modulos/servicio_nutrinext/registro', array_merge($this->menuData(), $extras));
    }

    public function registrar()
    {
        if ($deny = $this->denyIfNotSa()) {
            return $deny;
        }

        $model = new ServicioNutrinext();
        $post  = $this->collectPost();

        if ($post['codigo'] === '') {
            $post['codigo'] = $model->generarCodigo($post['nombre']);
            $this->request->setGlobal('post', array_merge($this->request->getPost(), ['codigo' => $post['codigo']]));
        }

        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->skipValidation(true);
        if ($model->insert($post)) {
            $id = (int) $model->getInsertID();
            $model->syncPaquetes($id, $this->request->getPost('paquetes') ?? []);

            return redirect()->to(base_url('dashboard/servicio-nutrinext/lista'))
                ->with('success', 'Servicio NutriNext registrado correctamente');
        }

        $msg = $this->formatModelErrors($model) ?: 'No se pudo guardar el servicio';

        return redirect()->back()->withInput()->with('errors', $msg);
    }

    public function update()
    {
        if ($deny = $this->denyIfNotSa()) {
            return $deny;
        }

        $model = new ServicioNutrinext();
        $id    = (int) $this->request->getPost('id');
        $item  = $model->find($id);
        if (! $item) {
            return redirect()->to(base_url('dashboard/servicio-nutrinext/lista'))
                ->with('error', 'Servicio no encontrado');
        }

        $post = $this->collectPost();
        if (! $this->validate($this->validationRules($id))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->skipValidation(true);
        $updated = $model->update($id, $post);

        if ($updated === false && ! empty($model->errors())) {
            return redirect()->back()->withInput()->with('errors', $this->formatModelErrors($model));
        }

        if ($updated === false && $model->find($id) === null) {
            return redirect()->back()->withInput()->with('errors', 'No se pudo actualizar el registro');
        }

        $model->syncPaquetes($id, $this->request->getPost('paquetes') ?? []);

        return redirect()->to(base_url('dashboard/servicio-nutrinext/lista'))
            ->with('success', 'Servicio NutriNext actualizado');
    }

    public function eliminar()
    {
        if ($deny = $this->denyIfNotSa()) {
            return $deny;
        }

        $model = new ServicioNutrinext();
        $id    = (int) $this->request->getPost('id');

        if ($model->delete($id)) {
            \Config\Database::connect()
                ->table('servicio_nutrinext_paquete')
                ->where('servicio_nutrinext_id', $id)
                ->delete();

            return $this->redirectWithFilters(
                base_url('dashboard/servicio-nutrinext/lista'),
                'Servicio eliminado',
                'success'
            );
        }

        return $this->redirectWithFilters(
            base_url('dashboard/servicio-nutrinext/lista'),
            'Error al eliminar',
            'errors'
        );
    }

    private function formExtras(): array
    {
        $moduloModel = new Modulo();

        return [
            'item'       => null,
            'categorias' => ServicioNutrinext::categorias(),
            'modulos'    => $moduloModel->where('estado', 'A')->orderBy('nombre', 'ASC')->findAll(),
            'paquetes'   => (new Paquete())->getPaquetesActivos(),
            'paquetes_seleccionados' => [],
        ];
    }

    private function collectPost(): array
    {
        $moduloId = $this->request->getPost('modulo_id');
        $color    = trim((string) $this->request->getPost('color'));

        return [
            'codigo'                 => trim((string) $this->request->getPost('codigo')),
            'nombre'                 => trim((string) $this->request->getPost('nombre')),
            'categoria'              => $this->request->getPost('categoria'),
            'icono'                  => $this->request->getPost('icono') ?: 'fas fa-circle',
            'color'                  => $color !== '' ? $color : '#7bc143',
            'descripcion_corta'      => $this->request->getPost('descripcion_corta'),
            'descripcion_larga'      => $this->request->getPost('descripcion_larga'),
            'beneficios'             => $this->request->getPost('beneficios'),
            'incluye'                => $this->request->getPost('incluye'),
            'etiquetas'              => $this->request->getPost('etiquetas'),
            'modulo_id'              => ($moduloId !== null && $moduloId !== '') ? (int) $moduloId : null,
            'ruta_dashboard'         => $this->request->getPost('ruta_dashboard'),
            'requiere_configuracion' => $this->request->getPost('requiere_configuracion') ?: 'N',
            'nota_configuracion'     => $this->request->getPost('nota_configuracion'),
            'visible_web'            => $this->request->getPost('visible_web') ?: 'N',
            'visible_catalogo'       => $this->request->getPost('visible_catalogo') ?: 'S',
            'destacado'              => $this->request->getPost('destacado') ?: 'N',
            'orden'                  => (int) ($this->request->getPost('orden') ?: 0),
            'estado'                 => $this->request->getPost('estado') ?: 'A',
            'documentacion_url'      => $this->request->getPost('documentacion_url'),
            'video_url'              => $this->request->getPost('video_url'),
        ];
    }

    private function validationRules(?int $id = null): array
    {
        $rules = config('Validation')->formServicioNutrinext;
        if ($id) {
            $rules['codigo']['rules'] = 'required|max_length[80]|is_unique[servicio_nutrinext.codigo,id,' . $id . ']';
        }

        return $rules;
    }

    private function formatModelErrors(ServicioNutrinext $model): ?string
    {
        $errors = $model->errors();
        if (empty($errors)) {
            return null;
        }

        if (is_string($errors)) {
            return $errors;
        }

        $lines = [];
        foreach ($errors as $field => $message) {
            if (is_array($message)) {
                $lines = array_merge($lines, $message);
            } else {
                $lines[] = is_numeric($field) ? $message : ($field . ': ' . $message);
            }
        }

        return implode("\n", $lines);
    }
}
