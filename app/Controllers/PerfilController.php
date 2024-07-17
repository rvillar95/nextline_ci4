<?php

namespace App\Controllers;

use App\Models\ModuloDetalle;
use App\Models\Perfil;

class PerfilController extends BaseController
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
        echo view('perfil/registro', $data);
    }

    public function registrar()
    {
        if (!$this->validate('formPerfilRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $perfilModel = new Perfil();

        $post = $this->request->getPost(['nombre', 'estado']);
        $data = [
            'nombre' => $post['nombre'],
            'estado' => $post['estado']
        ];

        if ($perfilModel->insert($data)) {
            return redirect()->to(base_url('dashboard/perfil/registro'))->with('success', 'Perfil registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el perfil');
        }
    }

    public function getPerfiles()
    {
        $perfilModel = new Perfil();
        $draw = intval($this->request->getGet("draw"));
        $books = $perfilModel->getPerfilAll();
    
        $data = array();
        foreach ($books as $r) {
            $data[] = array(
                $r->nombre,
                $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                '<a href="editar/'.$r->id.'" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 p-1 br-8 mb-1"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $perfilModel->countAll(),
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
        $perfil = new Perfil();
        $data['perfiles'] = $perfil->findAll();
        echo view('perfil/lista',$data);
    }

    public function editar($id)
    {
        $perfilModel = new Perfil();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['perfil'] = $perfilModel->select('perfil.*')
        ->where('perfil.id', $id)->first();
        //$data['perfil'] = $perfilModel->getPerfil($id);
        echo view("perfil/editar", $data);
    }

    public function update()
    {

        if (!$this->validate('formPerfilRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $perfilModel = new Perfil();
        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');
        $estado = $this->request->getPost('estado');


        if ($perfilModel->update($id, [
            'nombre' => $nombre,
            'estado' => $estado
        ])) {
            return redirect()->to(base_url('dashboard/perfil/editar/' . $id))->with('success', 'Perfil editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el perfil');
        }
    }

}
