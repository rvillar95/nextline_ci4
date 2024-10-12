<?php

namespace App\Controllers;

use App\Models\ModuloDetalle;
use App\Models\Perfil;
use App\Models\Modulo;

class ModuloController extends BaseController
{

    public function registro()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        echo view('Base/modulo/registro', $data);
    }

    public function registrar()
    {
        if (!$this->validate('formModuloRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $modulo = new Modulo();

        $post = $this->request->getPost(['nombre', 'descripcion', 'ruta', 'sa', 'estado', 'mostrar']);
        $data = [
            'nombre' => $post['nombre'],
            'descripcion' => $post['descripcion'],
            'ruta' => $post['ruta'],
            'sa' => $post['sa'],
            'estado' => $post['estado'],
            'mostrar' => $post['mostrar'],
        ];

      
        if ($modulo->insert($data)) {
            return redirect()->to(base_url('dashboard/modulo/registro'))->with('success', 'Modulo registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el Modulo');
        }
    }

    public function getModulos()
    {
        $modulo = new Modulo();
        $draw = intval($this->request->getGet("draw"));
        $books = $modulo->getModuloAll();

        $data = array();
        foreach ($books as $r) {
            $data[] = array(
                $r->nombre,
                $r->descripcion,
                $r->ruta,
                $r->sa == 'S' ? '<span class="badge badge-success mb-2 me-4">Si</span>' : '<span class="badge badge-danger mb-2 me-4">No</span>',
                $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                $r->mostrar == 'S' ? '<span class="badge badge-success mb-2 me-4">Si</span>' : '<span class="badge badge-danger mb-2 me-4">No</span>',
                '<a href="editar/' . $r->id . '" style="display:inline-block;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 p-1 br-8 mb-1"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $modulo->countAll(),
            "recordsFiltered" => 5,
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function editar($id)
    {
        $moduloPerfil = new Modulo();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['modulo'] = $moduloPerfil->select('modulo.*')
            ->where('modulo.id', $id)->first();

        echo view("Base/modulo/editar", $data);
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
        echo view('Base/modulo/lista', $data);
    }

    public function update()
    {

        if (!$this->validate('formModuloEdit')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $modulo = new Modulo();
        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');
        $descripcion = $this->request->getPost('descripcion');
        $ruta = $this->request->getPost('ruta');
        $sa = $this->request->getPost('sa');
        $mostrar = $this->request->getPost('mostrar');
        $estado = $this->request->getPost('estado');

        if ($modulo->update($id, [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'ruta' => $ruta,
            'sa' => $sa,
            'mostrar' => $mostrar,
            'estado' => $estado
        ])) {
            return redirect()->to(base_url('dashboard/modulo/editar/' . $id))->with('success', 'Modulo editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el modulo');
        }
    }

    public function eliminar()
    {
        $moduloModel = new Modulo();
        $id = $this->request->getPost('id');
        // Intenta eliminar el usuario
        if ($moduloModel->delete($id)) {
            // Usuario eliminado con éxito
            return redirect()->to(base_url('dashboard/modulo/lista'))->with('success', 'Modulo eliminado con éxito.');
        } else {
            // Error al eliminar el usuario
            return redirect()->to(base_url('dashboard/modulo/lista'))->with('error', 'No se pudo eliminar el modulo.');
        }
    }
}
