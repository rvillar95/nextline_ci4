<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\ModuloDetalle;
use App\Models\Modulo;
use App\Traits\MaintainsFilters;


class ModuloDetalleController extends BaseController
{
    use MaintainsFilters;

    public function registro()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $moduloModel = new Modulo();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }

        $data['modulos'] = $moduloModel->getActiveModulo();
        $data['data'] = $menuTotal;
        echo view('Base/modulo_detalle/registro', $data);
    }

    public function registrar()
    {
        if (!$this->validate('formModuloDetalleRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $moduloModel = new ModuloDetalle();

        $post = $this->request->getPost(['modulo', 'descripcion', 'menu_etiqueta', 'ruta', 'accion', 'estado', 'mostrar', 'orden']);
        $data = [
            'modulo_id' => $post['modulo'],
            'descripcion' => $post['descripcion'],
            'menu_etiqueta' => trim((string) ($post['menu_etiqueta'] ?? '')) ?: null,
            'ruta' => $post['ruta'],
            'accion' => $post['accion'],
            'estado' => $post['estado'],
            'mostrar' => $post['mostrar'],
            'orden' => $post['orden']
        ];

        if ($moduloModel->insert($data)) {
            return redirect()->to(base_url('dashboard/modulo-detalle/registro'))->with('success', 'Detalle Modulo registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el detalle modulo');
        }
    }

    public function getModuloDetalle()
    {
        $draw      = (int) ($this->request->getGet('draw') ?? 1);
        $start     = (int) ($this->request->getGet('start') ?? 0);
        $length    = (int) ($this->request->getGet('length') ?? 10);
        $searchVal = (string) ($this->request->getGet('search')['value'] ?? '');
        $moduloId  = $this->request->getGet('modulo_id');
        $moduloId  = ($moduloId === '' || $moduloId === null) ? null : (int) $moduloId;

        // Orden
        $orderIdx = (int) ($this->request->getGet('order')[0]['column'] ?? 0);
        $orderDir = (string) ($this->request->getGet('order')[0]['dir'] ?? 'asc');
        $orderable = [
            0 => 'm.nombre',    // Módulo
            1 => 'md.descripcion',
            2 => 'md.ruta',
            6 => 'md.orden',
        ];
        $orderBy = $orderable[$orderIdx] ?? 'md.id';

        $model = new ModuloDetalle();

        $recordsTotal    = $model->countAllMD();
        $recordsFiltered = $model->countFilteredMD($moduloId, $searchVal);
        $rows            = $model->fetchPageMD($moduloId, $searchVal, $orderBy, $orderDir, $start, $length);

        $data = array_map(static function(array $r): array {
            $id = (int)$r['id'];
            $mostrarActual = ($r['mostrar'] ?? 'S') === 'S' ? 'S' : 'N';
            $estadoActual = ($r['estado'] ?? 'A') === 'A' ? 'A' : 'I';
            
            // Select editable para "Mostrar"
            $mostrarSelect = '<select class="form-select form-select-sm editar-inline" data-id="' . $id . '" data-campo="mostrar" style="min-width: 80px;">' .
                '<option value="S" ' . ($mostrarActual === 'S' ? 'selected' : '') . '>Sí</option>' .
                '<option value="N" ' . ($mostrarActual === 'N' ? 'selected' : '') . '>No</option>' .
                '</select>';
            
            // Select editable para "Estado"
            $estadoSelect = '<select class="form-select form-select-sm editar-inline" data-id="' . $id . '" data-campo="estado" style="min-width: 100px;">' .
                '<option value="A" ' . ($estadoActual === 'A' ? 'selected' : '') . '>Activo</option>' .
                '<option value="I" ' . ($estadoActual === 'I' ? 'selected' : '') . '>Inactivo</option>' .
                '</select>';

            return [
                'modulo_nombre' => esc($r['modulo_nombre']),
                'descripcion'   => esc((string)$r['descripcion']),
                'ruta'          => esc((string)$r['ruta']),
                'accion'        => esc((string)$r['accion']),
                'mostrar_html'  => $mostrarSelect,
                'estado_html'   => $estadoSelect,
                'orden'         => (int)$r['orden'],
                'acciones_html' =>
                    '<a href="'.base_url('dashboard/modulo-detalle/editar/'.(int)$r['id']).'" class="bs-tooltip" title="Editar">' .
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>'
                    . '<button type="button" value="'.(int)$r['id'].'" id="btnEliminar" style="background:none;border:none;padding:0;cursor:pointer;display:inline-block;">'
                    . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>',
            ];
        }, $rows);

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function lista()
    {
        // menú lateral, si lo cargas igual que en otros módulos:
        $moduloMenu = new \App\Models\ModuloDetalle();
        $data['menu'] = $moduloMenu->getMenu(session()->get('usuario')['perfil_id'] ?? 0);
        $menuTotal = [];
        foreach ($data['menu'] as $entity) {
            $submenu = $moduloMenu->getSubMenu($entity['id']);
            $menuTotal[] = ['menu' => $entity, 'submenu' => $submenu];
        }
        $data['data'] = $menuTotal;

        // módulos para el <select>
        $modModel = new Modulo();
        $data['modulos'] = $modModel->getActiveModulo(); // ya lo usas en otros lados
        $data['selectedModuloId'] = (int) ($this->request->getGet('modulo_id') ?? 0);

        return view('Base/modulo_detalle/lista', $data);
    }

    public function editar($id)
    {
        $moduloModel = new Modulo();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['perfil'] = $modulo->getDetalleModulo($id);
        $data['modulos'] = $moduloModel->getActiveModulo();
        echo view("Base/modulo_detalle/editar", $data);
    }

    public function update()
    {

        if (!$this->validate('formModuloDetalleRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $moduloDetalle = new ModuloDetalle();
        $post = $this->request->getPost(['id', 'modulo', 'descripcion', 'menu_etiqueta', 'ruta', 'accion', 'estado', 'mostrar', 'orden']);
        $data = [
            'modulo_id' => $post['modulo'],
            'descripcion' => $post['descripcion'],
            'menu_etiqueta' => trim((string) ($post['menu_etiqueta'] ?? '')) ?: null,
            'ruta' => $post['ruta'],
            'accion' => $post['accion'],
            'estado' => $post['estado'],
            'mostrar' => $post['mostrar'],
            'orden' => $post['orden']
        ];

        if ($moduloDetalle->update($post['id'], $data)) {
            return redirect()->to(base_url('dashboard/modulo-detalle/editar/' . $post['id']))->with('success', 'Detalle Perfil editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el detalle perfil');
        }
    }

    public function eliminar()
    {
        $moduloDetalle = new ModuloDetalle();
        $id = $this->request->getPost('id');
        
        if ($moduloDetalle->delete($id)) {
            return $this->redirectWithPostFilters(base_url('dashboard/modulo-detalle/lista'), 'Modulo Detalle eliminado con éxito.', 'success');
        } else {
            return $this->redirectWithPostFilters(base_url('dashboard/modulo-detalle/lista'), 'No se pudo eliminar el Modulo Detalle.', 'errors');
        }
    }

    /**
     * Actualizar campo inline (mostrar o estado) vía AJAX
     */
    public function updateCampoInline()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No autorizado'
            ])->setStatusCode(401);
        }

        $id = $this->request->getPost('id');
        $campo = $this->request->getPost('campo'); // 'mostrar' o 'estado'
        $valor = $this->request->getPost('valor');

        if (!$id || !$campo || !$valor) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Datos incompletos'
            ])->setStatusCode(400);
        }

        // Validar que el campo sea permitido
        if (!in_array($campo, ['mostrar', 'estado'])) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Campo no permitido'
            ])->setStatusCode(400);
        }

        // Validar valores
        if ($campo === 'mostrar' && !in_array($valor, ['S', 'N'])) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Valor inválido para mostrar'
            ])->setStatusCode(400);
        }

        if ($campo === 'estado' && !in_array($valor, ['A', 'I'])) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Valor inválido para estado'
            ])->setStatusCode(400);
        }

        $moduloDetalle = new ModuloDetalle();
        
        if ($moduloDetalle->update($id, [$campo => $valor])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => ucfirst($campo) . ' actualizado correctamente',
                'csrf_token' => csrf_hash()
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al actualizar el campo',
                'csrf_token' => csrf_hash()
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(500);
        }
    }
}
