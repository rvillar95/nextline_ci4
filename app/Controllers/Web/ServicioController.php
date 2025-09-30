<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\ModuloDetalle;
use App\Models\Servicio;

class ServicioController extends BaseController
{
    public function index()
    {
        $servicioModel = new Servicio();
        $servicioCategoriaModel = new \App\Models\ServicioCategoria();
        $categoria = $this->request->getGet('categoria');
        
        // Obtener servicios con información de categoría
        $servicios = $servicioModel->getServiciosPorCategoria($categoria);
        
        // Obtener todas las categorías activas con iconos para los filtros
        $categorias = $servicioCategoriaModel->select('*')
                                           ->where('estado', 'A')
                                           ->orderBy('orden', 'ASC')
                                           ->orderBy('nombre', 'ASC')
                                           ->findAll();
        
        $data = [
            'title' => 'Nuestros Servicios - MANSANCHEZ Constructor',
            'description' => 'Servicios de construcción profesional: construcción residencial, comercial, remodelaciones y más.',
            'keywords' => 'servicios construcción, constructor, remodelaciones, obras, mansanchez',
            'servicios' => $servicios,
            'categorias' => $categorias,
            'categoria_actual' => $categoria
        ];
        
        return view('Web/servicios', $data);
    }

    public function detalle($slug)
    {
        $servicioModel = new Servicio();
        $servicio = $servicioModel->getServicioPorSlug($slug);
        
        if (!$servicio) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Servicio no encontrado');
        }
        
        // Obtener servicios relacionados
        $servicios_relacionados = $servicioModel->getServiciosConCategoria($servicio->categoria_id);
        
        // Filtrar el servicio actual de los relacionados
        $servicios_relacionados = array_filter($servicios_relacionados, function($s) use ($servicio) {
            return $s->id != $servicio->id;
        });
        
        // Limitar a 3 servicios relacionados
        $servicios_relacionados = array_slice($servicios_relacionados, 0, 3);
        
        $data = [
            'title' => $servicio->nombre . ' - NextLine Constructor',
            'description' => $servicio->descripcionCorta,
            'keywords' => $servicio->nombre . ', ' . ($servicio->categoria_nombre ?? $servicio->categoria) . ', constructor',
            'servicio' => $servicio,
            'servicios_relacionados' => $servicios_relacionados
        ];
        
        return view('Web/servicio_detalle', $data);
    }

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

        
        $post = $this->request->getPost([
            'nombre', 'categoria', 'descripcionCorta', 'descripcionLarga', 
            'caracteristicas', 'beneficios', 'tiempo_estimado', 'garantia',
            'precio_desde', 'precio_hasta', 'mostrar_precio', 'estado', 'orden', 'destacado'
        ]);
        
        $data = [
            'nombre' => $post['nombre'],
            'categoria' => $post['categoria'],
            'descripcionCorta' => $post['descripcionCorta'],
            'descripcionLarga' => $post['descripcionLarga'],
            'caracteristicas' => $post['caracteristicas'],
            'beneficios' => $post['beneficios'],
            'tiempo_estimado' => $post['tiempo_estimado'],
            'garantia' => $post['garantia'],
            'precio_desde' => $post['precio_desde'],
            'precio_hasta' => $post['precio_hasta'],
            'mostrar_precio' => $post['mostrar_precio'] ?? 'S',
            'estado' => $post['estado'],
            'orden' => $post['orden'] ?? 0,
            'destacado' => $post['destacado'] ?? 'N'
        ];
        
        // Generar slug automáticamente
        $servicioModel = new Servicio();
        $data['slug'] = $servicioModel->generarSlug($post['nombre']);
        
        // Manejar imagen
        if ($img->isValid() && ! $img->hasMoved()) {
            $fecha = date('dmY');
            $newName = $img->getRandomName();
            $uploadPath = ROOTPATH . 'lib/img/' . $fecha . '/';
            
            // Crear directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            if ($img->move($uploadPath, $newName)) {
                $data['foto'] = 'lib/img/' . $fecha . '/' . $newName;
            } else {
                return redirect()->back()->withInput()->with('errors', 'Error al subir la imagen');
            }
        } else {
            $data['foto'] = 'lib/images/default-service.jpg'; // Imagen por defecto
        }

        if ($servicio->insert($data)) {
            return redirect()->to(base_url('dashboard/servicio/lista'))->with('success', 'Servicio registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el Servicio');
        }
    }

    public function getServicio()
    {
        $servicio = new Servicio();
        $draw = intval($this->request->getGet("draw"));
        $books = $servicio->getServicioAll();

        $data = array();
        foreach ($books as $r) {
            // Formatear precio
            $precio = '';
            if ($r->mostrar_precio === 'S') {
                if ($r->precio_desde && $r->precio_hasta) {
                    $precio = '$' . number_format($r->precio_desde, 0, ',', '.') . ' - $' . number_format($r->precio_hasta, 0, ',', '.');
                } elseif ($r->precio_desde) {
                    $precio = 'Desde $' . number_format($r->precio_desde, 0, ',', '.');
                } elseif ($r->precio_hasta) {
                    $precio = 'Hasta $' . number_format($r->precio_hasta, 0, ',', '.');
                } elseif ($r->valor) {
                    $precio = '$' . number_format($r->valor, 0, ',', '.');
                }
            } else {
                $precio = 'Consultar precio';
            }

            $data[] = array(
                $r->nombre,
                $r->categoria ?? 'Sin categoría',
                $r->descripcionCorta,
                $precio,
                $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                $r->destacado == 'S' ? '<span class="badge badge-warning mb-2 me-4">⭐ Destacado</span>' : '<span class="badge badge-secondary mb-2 me-4">Normal</span>',
                "<img src='".base_url($r->foto)."' style='max-width: 50px; height: 50px; object-fit: cover; border-radius: 5px;' />",
                '<a href="editar/' . $r->id . '" style="display:inline-block; margin-right: 5px;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $servicio->countAll(),
            "recordsFiltered" => $servicio->countAll(),
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

    public function eliminar()
    {
        $servicio = new Servicio();
        $id = $this->request->getPost('id');
        
        if ($servicio->delete($id)) {
            return redirect()->to(base_url('dashboard/servicio/lista'))->with('success', 'Servicio eliminado con éxito');
        } else {
            return redirect()->back()->with('errors', 'Error al eliminar el servicio');
        }
    }

    public function update()
    {

        if (!$this->validate('formServiceEdit')) {
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
}
