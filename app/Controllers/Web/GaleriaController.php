<?php

namespace App\Controllers\Web;
use App\Controllers\BaseController;
use DateTime;
use App\Models\Galeria;
use App\Models\GaleriaCategoria;
use App\Models\ModuloDetalle;

class GaleriaController extends BaseController
{
    public function index()
    {
        $galeriaModel = new Galeria();
        $galeriaCategoriaModel = new GaleriaCategoria();
        $categoria = $this->request->getGet('categoria');
        
        // Obtener proyectos con información de categoría
        $proyectos = $galeriaModel->getGaleriaPorCategoria($categoria);
        
        // Obtener todas las categorías activas con iconos para los filtros
        $categorias = $galeriaCategoriaModel->select('*')
                                          ->where('estado', 'A')
                                          ->orderBy('orden', 'ASC')
                                          ->orderBy('nombre', 'ASC')
                                          ->findAll();
        
        $data = [
            'title' => 'Nuestros Proyectos - MANSANCHEZ Constructor',
            'description' => 'Galería de proyectos de construcción: casas, edificios, remodelaciones y más.',
            'keywords' => 'proyectos construcción, galería obras, constructor, proyectos terminados, mansanchez',
            'proyectos' => $proyectos,
            'categorias' => $categorias,
            'categoria_actual' => $categoria
        ];
        
        return view('Web/proyectos', $data);
    }

    public function registro()
    {
        $galeriaCategoria = new GaleriaCategoria();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        $data['categorias'] = $galeriaCategoria->getCategoriasActivas();
        echo view('Modulos/galeria/registro', $data);
    }

    public function registrar()
    {

        $galeria = new Galeria();

        if (!$this->validate('formGaleriaRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $img = $this->request->getFile('portada');
        $post = $this->request->getPost(['nombre', 'categoria_id', 'descripcion', 'estado']);
        
        $portadaPath = '';
        if ($img->isValid() && ! $img->hasMoved()) {
            $fecha = date('dmY');
            $newName = $img->getRandomName();
            
            // Crear directorio si no existe
            $uploadPath = ROOTPATH . 'lib/img/' . $fecha . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            if ($img->move($uploadPath, $newName)) {
                $portadaPath = 'lib/img/' . $fecha . '/' . $newName;
            } else {
                return redirect()->back()->withInput()->with('errors', 'Error al subir la imagen');
            }
        } else {
            return redirect()->back()->withInput()->with('errors', 'Por favor selecciona una imagen válida');
        }

        $data = [
            'nombre' => $post['nombre'],
            'categoria_id' => $post['categoria_id'] ?: null,
            'descripcion' => $post['descripcion'],
            'portada' => $portadaPath,
            'estado' => $post['estado']
        ];

        if ($galeria->insert($data)) {
            return redirect()->to(base_url('dashboard/galeria/lista'))->with('success', 'Galería registrada con éxito');
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
                isset($r->categoria_nombre) ? $r->categoria_nombre : 'Sin categoría',
                $r->descripcion,
                $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                "<img src='" . base_url($r->portada) . "' style='max-width: 100%; height: auto;' />",
                '<button type="button" value="' . $r->id . '" id="btnVerDetalle" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Ver detalle" aria-label="Ver detalle" data-bs-original-title="Ver detalle" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block; margin-right:5px;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                <a href="editar/' . $r->id . '" style="display:inline-block; margin-right:5px;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
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
        $galeriaCategoria = new GaleriaCategoria();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['galeria'] = $galeria->getGaleriaConCategoria($id);
        $data['categorias'] = $galeriaCategoria->getCategoriasActivas();

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
        $categoria_id = $this->request->getPost('categoria_id');
        $descripcion = $this->request->getPost('descripcion');
        $estado = $this->request->getPost('estado');
        $arreglo = [
            'nombre' => $nombre,
            'categoria_id' => $categoria_id ?: null,
            'descripcion' => $descripcion,
            'estado' => $estado
        ];
        $img = $this->request->getFile('portada');
        if ($img && $img->isValid() && ! $img->hasMoved()) {
            $fechaPath = date('dmY');
            $newName = $img->getRandomName();
            
            // Crear directorio si no existe
            $uploadPath = ROOTPATH . 'lib/img/' . $fechaPath . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            if ($img->move($uploadPath, $newName)) {
                $arreglo['portada'] = 'lib/img/' . $fechaPath . '/' . $newName;
            } else {
                return redirect()->back()->withInput()->with('errors', 'Error al subir la nueva imagen');
            }
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
