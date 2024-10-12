<?php

namespace App\Controllers;

use App\Models\ModuloDetalle;
use App\Models\Servicio;
use App\Models\Modulo;
use CodeIgniter\Commands\Server\Serve;

class ServicioController extends BaseController
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
        echo view('Modulos/servicio/registro', $data);
    }

    public function registrar()
    {

        $servicio = new Servicio();

        if (!$this->validate('formServiceRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $img = $this->request->getFile('img');

        
        $post = $this->request->getPost(['nombre', 'descripcionCorta', 'descripcionLarga', 'valor', 'estado']);
        if ($img->isValid() && ! $img->hasMoved()) {
            $fecha = date('dmY');
            $newName = $img->getRandomName();
            $img->move(ROOTPATH . 'lib/img/'.$fecha.'/', $newName);
        }
        $data = [
            'nombre' => $post['nombre'],
            'descripcionCorta' => $post['descripcionCorta'],
            'descripcionLarga' => $post['descripcionLarga'],
            'valor' => $post['valor'],
            'foto' => 'lib/img/'.$fecha.'/' . $newName,
            'estado' => $post['estado']
        ];

        if ($servicio->insert($data)) {
            return redirect()->to(base_url('dashboard/servicio/registro'))->with('success', 'Servicio registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el Modulo');
        }
    }

    public function getServicio()
    {
        $servicio = new Servicio();
        $draw = intval($this->request->getGet("draw"));
        $books = $servicio->getServicioAll();

        $data = array();
        foreach ($books as $r) {
            $data[] = array(
                $r->nombre,
                $r->descripcionCorta,
                $r->descripcionLarga,
                $r->valor,
                "<img src='".base_url($r->foto)."' style='max-width: 100%; height: auto;' />",
                $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                '<a href="editar/' . $r->id . '" style="display:inline-block;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 p-1 br-8 mb-1"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $servicio->countAll(),
            "recordsFiltered" => 5,
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function editar($id)
    {
        $servicio = new Servicio();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['servicio'] = $servicio->select('servicio.*')
            ->where('servicio.id', $id)->first();

        echo view("Modulos/servicio/editar", $data);
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
        echo view('Modulos/servicio/lista', $data);
    }

    public function update()
    {

        if (!$this->validate('formServiceEdit')) {
            echo "<pre>";
            print_r($this->validator->getErrors());
            echo "</pre>";
            exit();
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $servicio = new Servicio();

        $arreglo = [];

        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');
        $descripcionLarga = $this->request->getPost('descripcionLarga');
        $descripcionCorta = $this->request->getPost('descripcionCorta');
        $valor = $this->request->getPost('valor');
        $estado = $this->request->getPost('estado');
        $arreglo = [
            'nombre' => $nombre,
            'descripcionLarga' => $descripcionLarga,
            'descripcionCorta' => $descripcionCorta,
            'valor' => $valor,
            'estado' => $estado
        ];
        $img = $this->request->getFile('img');
        if ($img->isValid() && ! $img->hasMoved()) {
            $fecha = date('dmY');
            $newName = $img->getRandomName();
            $img->move(ROOTPATH . 'lib/img/'.$fecha.'/', $newName);
            $arreglo = [
                'nombre' => $nombre,
                'descripcionLarga' => $descripcionLarga,
                'descripcionCorta' => $descripcionCorta,
                'valor' => $valor,
                'estado' => $estado,
                'foto' => 'lib/img/'.$fecha.'/' . $newName
            ];
        }

        if ($servicio->update($id, $arreglo)) {
            return redirect()->to(base_url('dashboard/servicio/editar/' . $id))->with('success', 'Servicio editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el servicio');
        }
    }

    public function eliminar()
    {
        $servicio = new Servicio();
        $id = $this->request->getPost('id');
        // Intenta eliminar el usuario
        if ($servicio->delete($id)) {
            // Usuario eliminado con éxito
            return redirect()->to(base_url('dashboard/servicio/lista'))->with('success', 'Servicio eliminado con éxito.');
        } else {
            // Error al eliminar el usuario
            return redirect()->to(base_url('dashboard/servicio/lista'))->with('error', 'No se pudo eliminar el servicio.');
        }
    }
}
