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

        $data['perfiles'] = $perfilModel->getActivePerfil();
        $data['modulos'] = $moduloModel->getActiveModulo();
        $data['data'] = $menuTotal;
        echo view('perfil_detalle/registro', $data);
    }

    public function registrar()
    {
        // echo "hola1";

        if (!$this->validate('formPerfilDetalleRegister')) {

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        // echo "hola3";
        //exit;
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

        $perfilModel = new PerfilModulo();
        $draw = intval($this->request->getGet("draw"));
        $books = $perfilModel->getPerfilModuloAll();

        $data = array();
        foreach ($books as $r) {
            $data[] = array(
                $r->nombrePerfil,
                $r->nombreModulo,
                $r->ver,
                $r->registrar,
                $r->editar,
                $r->eliminar,
                $r->orden,
                $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                '<a href="editar/' . $r->id . '" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 p-1 br-8 mb-1"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>'
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
        echo view('perfil_detalle/lista', $data);
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

        $data['perfil'] = $perfilModulo->select('perfil_modulo.*')
            ->where('perfil_modulo.id', $id)->first();

        $data['perfiles'] = $perfilModel->getActivePerfil();
        $data['modulos'] = $moduloModel->getActiveModulo();
        echo view("perfil_detalle/editar", $data);
    }

    public function update()
    {

        if (!$this->validate('formPerfilDetalleRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $perfilModel = new PerfilModulo();
        $post = $this->request->getPost(['id','perfil', 'modulo', 'ver', 'registrar', 'editar', 'eliminar', 'orden', 'estado']);
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
}
