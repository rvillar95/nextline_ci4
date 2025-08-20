<?php

namespace App\Controllers;

use App\Models\ModuloDetalle;
use App\Models\PerfilModulo;
use App\Models\Perfil;
use App\Models\Modulo;


class PerfilDetalleController extends BaseController
{

    public function registro()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $perfilModel = new Perfil();
        $moduloModel = new Modulo();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }

        $data['perfiles'] = $perfilModel->getActivePerfil($this->poder);
        $data['modulos'] = $moduloModel->getActiveModulo();
        $data['data'] = $menuTotal;
        echo view('Base/perfil_detalle/registro', $data);
    }

    public function registrar()
    {
        if (!$this->validate('formPerfilDetalleRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $perfilModulo = new PerfilModulo();

        $post = $this->request->getPost(['perfil', 'modulo', 'ver', 'registrar', 'editar', 'eliminar', 'orden']);
        $data = [
            'perfil_id' => $post['perfil'],
            'modulo_id' => $post['modulo'],
            'ver' => $post['ver'],
            'registrar' => $post['registrar'],
            'editar' => $post['editar'],
            'eliminar' => $post['eliminar'],
            'estado' => 'A',
            'orden' => $post['orden']
        ];

        if ($perfilModulo->insert($data)) {
            return redirect()->to(base_url('dashboard/perfil-detalle/registro'))->with('success', 'Detalle Perfil registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el detalle perfil');
        }
    }
    public function getPerfilDetalle()
    {
        // Parámetros DataTables
        $draw      = (int) ($this->request->getGet('draw') ?? 1);
        $start     = (int) ($this->request->getGet('start') ?? 0);
        $length    = (int) ($this->request->getGet('length') ?? 10);
        $searchVal = (string) ($this->request->getGet('search')['value'] ?? '');
        $perfilId  = $this->request->getGet('perfil_id');
        $perfilId  = ($perfilId === '' || $perfilId === null) ? null : (int) $perfilId;

        // Orden
        $orderColIdx = (int) ($this->request->getGet('order')[0]['column'] ?? 0);
        $orderDir    = (string) ($this->request->getGet('order')[0]['dir'] ?? 'asc');
        $orderable   = [
            0 => 'p.nombre',                 // Perfil
            1 => 'm.nombre',                 // Módulo
            6 => 'pm.orden',                 // Orden
        ];
        $orderBy = $orderable[$orderColIdx] ?? 'pm.id';

        $poder = $this->poder;

        $model = new PerfilModulo();

        // Conteos
        $recordsTotal    = $model->countAllByPower($poder);
        $recordsFiltered = $model->countFiltered($poder, $perfilId, $searchVal);

        // Página
        $rows = $model->fetchPage($poder, $perfilId, $searchVal, $orderBy, $orderDir, $start, $length);

        // Mapear a columnas HTML (evitar XSS con esc())
        $data = array_map(static function (array $r): array {
            $badge = fn(bool $ok) => $ok
                ? '<span class="badge badge-success mb-2 me-4">Permitido</span>'
                : '<span class="badge badge-danger mb-2 me-4">No Permitido</span>';

            return [
                'perfil_nombre'   => esc($r['perfil_nombre']),
                'modulo_nombre'   => esc($r['modulo_nombre']),
                'ver_html'        => $badge((int)$r['ver'] === 1),
                'registrar_html'  => $badge((int)$r['registrar'] === 1),
                'editar_html'     => $badge((int)$r['editar'] === 1),
                'eliminar_html'   => $badge((int)$r['eliminar'] === 1),
                'orden'           => (int)$r['orden'],
                'estado_html'     => $r['estado'] === 'A'
                    ? '<span class="badge badge-success mb-2 me-4">Activo</span>'
                    : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                'acciones_html'   => '<a href="' . base_url('dashboard/perfil-detalle/editar/' . (int)$r['id']) . '" class="bs-tooltip" data-bs-toggle="tooltip" title="Editar">'
                    . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>'
                    . '<button type="button" value="' . (int)$r['id'] . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;">'
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
        $perfilId = (int) ($this->request->getGet('perfil_id') ?? 0);
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $perfilModel = new Perfil();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        $data['selectedPerfilId'] = $perfilId;
        $data['perfiles'] = $perfilModel->getActivePerfil($this->poder);
        echo view('Base/perfil_detalle/lista', $data);
    }

    public function editar($id)
    {
        $perfilModel = new Perfil();
        $perfilModulo = new PerfilModulo();
        $moduloModel = new Modulo();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['perfil'] = $perfilModulo->getPerfilModulo($id);
        $data['perfiles'] = $perfilModel->getActivePerfil($this->poder);
        $data['modulos'] = $moduloModel->getActiveModulo();
        echo view("Base/perfil_detalle/editar", $data);
    }

    public function update()
    {

        if (!$this->validate('formPerfilDetalleRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $perfilModel = new PerfilModulo();
        $post = $this->request->getPost(['id', 'perfil', 'modulo', 'ver', 'registrar', 'editar', 'eliminar', 'orden', 'estado']);
        $data = [
            'perfil_id' => $post['perfil'],
            'modulo_id' => $post['modulo'],
            'ver' => $post['ver'],
            'registrar' => $post['registrar'],
            'editar' => $post['editar'],
            'eliminar' => $post['eliminar'],
            'estado' => $post['estado'],
            'orden' => $post['orden']
        ];


        if ($perfilModel->update($post['id'], $data)) {
            return redirect()->to(base_url('dashboard/perfil-detalle/editar/' . $post['id']))->with('success', 'Detalle Perfil editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el detalle perfil');
        }
    }

    public function eliminar()
    {
        $perfilModulo = new PerfilModulo();
        $id = $this->request->getPost('id');
        // Intenta eliminar el usuario
        if ($perfilModulo->delete($id)) {
            // Usuario eliminado con éxito
            return redirect()->to(base_url('dashboard/perfil-detalle/lista'))->with('success', 'Perfil Detalle eliminado con éxito.');
        } else {
            // Error al eliminar el usuario
            return redirect()->to(base_url('dashboard/perfil-detalle/lista'))->with('errors', 'No se pudo eliminar el Perfil Detalle.');
        }
    }
}
