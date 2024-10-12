<?php

namespace App\Controllers;

use App\Models\ModuloDetalle;
use App\Models\PerfilModulo;
use App\Models\Perfil;
use App\Models\Modulo;


class ModuloDetalleController extends BaseController
{

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

        $post = $this->request->getPost(['modulo', 'descripcion', 'ruta', 'accion', 'estado', 'mostrar', 'orden']);
        $data = [
            'modulo_id' => $post['modulo'],
            'descripcion' => $post['descripcion'],
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
        $moduloDetalle = new ModuloDetalle();
        $draw = intval($this->request->getGet("draw"));
        $books = $moduloDetalle->getModuloDetalleAll();

        $data = array();
        foreach ($books as $r) {
            $data[] = array(
                $r->nombreModulo,
                $r->descripcion,
                $r->ruta,
                $r->accion,
                $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                $r->mostrar == 'S' ? '<span class="badge badge-success mb-2 me-4">Si</span>' : '<span class="badge badge-danger mb-2 me-4">No</span>',
                $r->orden,
                '<a href="editar/' . $r->id . '" style="display:inline-block;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 p-1 br-8 mb-1"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $moduloDetalle->countAll(),
            "recordsFiltered" => 5,
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function lista()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        echo view('Base/modulo_detalle/lista', $data);
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
        $post = $this->request->getPost(['id', 'modulo', 'descripcion', 'ruta', 'accion', 'estado', 'mostrar', 'orden']);
        $data = [
            'modulo_id' => $post['modulo'],
            'descripcion' => $post['descripcion'],
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
        // Intenta eliminar el usuario
        if ($moduloDetalle->delete($id)) {
            // Usuario eliminado con éxito
            return redirect()->to(base_url('dashboard/modulo-detalle/lista'))->with('success', 'Modulo Detalle eliminado con éxito.');
        } else {
            // Error al eliminar el usuario
            return redirect()->to(base_url('dashboard/modulo-detalle/lista'))->with('errors', 'No se pudo eliminar el Modulo Detalle.');
        }
    }
}
