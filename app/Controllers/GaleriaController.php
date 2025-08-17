<?php

namespace App\Controllers;

use DateTime;
use App\Models\Galeria;
use App\Models\ModuloDetalle;
use App\Models\Servicio;
use App\Models\Modulo;
use CodeIgniter\Commands\Server\Serve;

class GaleriaController extends BaseController
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
        echo view('Modulos/galeria/registro', $data);
    }

    public function registrar()
    {

        $galeria = new Galeria();

        if (!$this->validate('formGaleriaRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $img = $this->request->getFile('portada');


        $post = $this->request->getPost(['nombre', 'descripcion', 'fecha', 'estado']);
        //echo $post['fecha'];
        //exit();
        if ($img->isValid() && ! $img->hasMoved()) {
            $fecha = date('dmY');
            $newName = $img->getRandomName();
            $img->move(ROOTPATH . 'lib/img/' . $fecha . '/', $newName);
        }
        $fechaPost = DateTime::createFromFormat('Y-m-d\TH:i', $post['fecha']);
        $fechaMysql = $fechaPost->format('Y-m-d H:i:s');

        $data = [
            'nombre' => $post['nombre'],
            'descripcion' => $post['descripcion'],
            'fecha' => $fechaMysql,
            'portada' => 'lib/img/' . $fecha . '/' . $newName,
            'estado' => $post['estado']
        ];

        if ($galeria->insert($data)) {
            return redirect()->to(base_url('dashboard/galeria/registro'))->with('success', 'Galeria registrada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar la Galeria');
        }
    }

    public function getGaleria()
    {
        $galeria = new Galeria();
        $draw = intval($this->request->getGet("draw"));
        $books = $galeria->getGaleriaAll();

        $data = array();
        foreach ($books as $r) {
            $data[] = array(
                $r->nombre,
                $r->descripcion,
                $r->fecha,
                "<img src='" . base_url($r->portada) . "' style='max-width: 100%; height: auto;' />",
                $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                '<a href="editar/' . $r->id . '" style="display:inline-block;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Eliminar galeria" aria-label="Eliminar galeria" data-bs-original-title="Eliminar galeria" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $galeria->countAll(),
            "recordsFiltered" => 5,
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function editar($id)
    {
        $galeria = new Galeria();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['galeria'] = $galeria->select('galeria.*')
            ->where('galeria.id', $id)->first();

        echo view("Modulos/galeria/editar", $data);
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
        echo view('Modulos/galeria/lista', $data);
    }

    public function update()
    {

        if (!$this->validate('formGaleriaEdit')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $galeria = new Galeria();

        $arreglo = [];

        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');
        $descripcion = $this->request->getPost('descripcion');
        $fecha = $this->request->getPost('fecha');
        $estado = $this->request->getPost('estado');
        $arreglo = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'fecha' => $fecha,
            'estado' => $estado
        ];
        $img = $this->request->getFile('portada');
        if ($img->isValid() && ! $img->hasMoved()) {
            $fechaPath = date('dmY');
            $newName = $img->getRandomName();
            $img->move(ROOTPATH . 'lib/img/' . $fechaPath . '/', $newName);
            $arreglo = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'fecha' => $fecha,
                'estado' => $estado,
                'portada' => 'lib/img/' . $fechaPath . '/' . $newName
            ];
        }

        if ($galeria->update($id, $arreglo)) {
            return redirect()->to(base_url('dashboard/galeria/editar/' . $id))->with('success', 'Galeria editada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el galeria');
        }
    }

    public function eliminar()
    {
        $galeria = new Galeria();
        $id = $this->request->getPost('id');
        // Intenta eliminar el usuario
        if ($galeria->delete($id)) {
            // Usuario eliminado con éxito
            return redirect()->to(base_url('dashboard/galeria/lista'))->with('success', 'Galeria eliminada con éxito.');
        } else {
            // Error al eliminar el usuario
            return redirect()->to(base_url('dashboard/galeria/lista'))->with('error', 'No se pudo eliminar la galeria.');
        }
    }
}
