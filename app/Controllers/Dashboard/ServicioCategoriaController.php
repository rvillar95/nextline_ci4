<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\ModuloDetalle;
use App\Models\ServicioCategoria;

class ServicioCategoriaController extends BaseController
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
        echo view('Modulos/servicio_categoria/registro', $data);
    }

    public function registrar()
    {
        $servicioCategoria = new ServicioCategoria();

        if (!$this->validate('formServicioCategoriaRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'nombre', 'descripcion', 'icono', 'color', 'estado', 'orden', 'meta_titulo', 'meta_descripcion', 'meta_keywords'
        ]);
        
        $data = [
            'nombre' => $post['nombre'],
            'slug' => $servicioCategoria->generarSlug($post['nombre']),
            'descripcion' => $post['descripcion'],
            'icono' => $post['icono'],
            'color' => $post['color'],
            'estado' => $post['estado'],
            'orden' => $post['orden'] ?? 0
        ];
        
        // Generar campos SEO automáticamente
        $seo = $servicioCategoria->generarSEO($post['nombre'], $post['descripcion']);
        $data = array_merge($data, $seo);

        if ($servicioCategoria->insert($data)) {
            return redirect()->to(base_url('dashboard/servicio-categoria/lista'))->with('success', 'Categoría registrada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar la categoría');
        }
    }

    public function getServicioCategoria()
    {
        $servicioCategoria = new ServicioCategoria();
        $draw = intval($this->request->getGet("draw"));
        $books = $servicioCategoria->getCategoriaConServicios();

        $data = array();
        foreach ($books as $r) {
            $data[] = array(
                "nombre" => $r->nombre,
                "descripcion" => $r->descripcion ?? 'Sin descripción',
                "icono" => $r->icono ? '<i class="' . $r->icono . '" style="color: ' . $r->color . '; font-size: 20px;"></i>' : 'Sin icono',
                "color" => $r->color ? '<span style="background-color: ' . $r->color . '; color: white; padding: 2px 8px; border-radius: 4px;">' . $r->color . '</span>' : 'Sin color',
                "servicios_count" => $r->total_servicios ?? 0,
                "orden" => $r->orden,
                "estado" => $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                "acciones" => '<a href="editar/' . $r->id . '" style="display:inline-block; margin-right: 5px;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $servicioCategoria->countAll(),
            "recordsFiltered" => $servicioCategoria->countAll(),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function editar($id)
    {
        $servicioCategoria = new ServicioCategoria();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['categoria'] = $servicioCategoria->find($id);

        echo view("Modulos/servicio_categoria/editar", $data);
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
        echo view('Modulos/servicio_categoria/lista', $data);
    }

    public function eliminar()
    {
        $servicioCategoria = new ServicioCategoria();
        $id = $this->request->getPost('id');
        
        if ($servicioCategoria->delete($id)) {
            return redirect()->to(base_url('dashboard/servicio-categoria/lista'))->with('success', 'Categoría eliminada con éxito');
        } else {
            return redirect()->back()->with('errors', 'Error al eliminar la categoría');
        }
    }

    public function update()
    {
        if (!$this->validate('formServicioCategoriaEdit')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $servicioCategoria = new ServicioCategoria();

        $id = $this->request->getPost('id');
        $post = $this->request->getPost([
            'nombre', 'descripcion', 'icono', 'color', 'estado', 'orden', 'meta_titulo', 'meta_descripcion', 'meta_keywords'
        ]);
        
        $arreglo = [
            'nombre' => $post['nombre'],
            'slug' => $servicioCategoria->generarSlug($post['nombre']),
            'descripcion' => $post['descripcion'],
            'icono' => $post['icono'],
            'color' => $post['color'],
            'estado' => $post['estado'],
            'orden' => $post['orden'] ?? 0
        ];
        
        // Generar campos SEO automáticamente
        $seo = $servicioCategoria->generarSEO($post['nombre'], $post['descripcion']);
        $arreglo = array_merge($arreglo, $seo);

        if ($servicioCategoria->update($id, $arreglo)) {
            return redirect()->to(base_url('dashboard/servicio-categoria/editar/' . $id))->with('success', 'Categoría editada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar la categoría');
        }
    }
}
