<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\ModuloDetalle;
use App\Models\Testimonio;
use App\Models\Proyecto;
use App\Models\Servicio;

class TestimonioController extends BaseController
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
        
        // Obtener proyectos y servicios para los selects
        $proyectoModel = new Proyecto();
        $servicioModel = new Servicio();
        
        $data['proyectos'] = $proyectoModel->where('estado', 'A')->findAll();
        $data['servicios'] = $servicioModel->where('estado', 'A')->findAll();

        echo view('Modulos/testimonio/registro', $data);
    }

    public function registrar()
    {
        $testimonio = new Testimonio();

        if (!$this->validate('formTestimonioRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'nombre', 'cargo', 'empresa', 'testimonio', 'calificacion',
            'proyecto_id', 'servicio_id', 'estado', 'destacado', 'fecha_proyecto'
        ]);

        // Generar slug y SEO
        $slug = $testimonio->generarSlug($post['nombre']);
        $seo = $testimonio->generarSEO($post['nombre'], $post['testimonio'], $post['empresa']);

        $data = [
            'nombre' => $post['nombre'],
            'cargo' => $post['cargo'],
            'empresa' => $post['empresa'],
            'testimonio' => $post['testimonio'],
            'calificacion' => $post['calificacion'],
            'proyecto_id' => !empty($post['proyecto_id']) ? $post['proyecto_id'] : null,
            'servicio_id' => !empty($post['servicio_id']) ? $post['servicio_id'] : null,
            'estado' => $post['estado'],
            'destacado' => $post['destacado'],
            'fecha_proyecto' => !empty($post['fecha_proyecto']) ? $post['fecha_proyecto'] : null,
            'slug' => $slug,
            'meta_titulo' => $seo['meta_titulo'],
            'meta_descripcion' => $seo['meta_descripcion'],
            'meta_keywords' => $seo['meta_keywords']
        ];

        if ($testimonio->insert($data)) {
            return redirect()->to(base_url('dashboard/testimonio/registro'))->with('success', 'Testimonio registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el testimonio');
        }
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

        echo view('Modulos/testimonio/lista', $data);
    }

    public function getTestimonios()
    {
        $testimonio = new Testimonio();
        $draw = intval($this->request->getGet("draw"));
        $rows = $testimonio->orderBy('id', 'DESC')->findAll();

        $data = array();
        foreach ($rows as $r) {
            $estado = $r['estado'] == 'A' ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>';
            $destacado = $r['destacado'] == 'S' ? '<span class="badge badge-warning">Destacado</span>' : '';
            
            $estrellas = str_repeat('★', $r['calificacion']) . str_repeat('☆', 5 - $r['calificacion']);
            
            $proyecto = '';
            if ($r['proyecto_id']) {
                $proyectoModel = new Proyecto();
                $proyectoData = $proyectoModel->find($r['proyecto_id']);
                $proyecto = $proyectoData ? $proyectoData['nombre'] : 'Proyecto no encontrado';
            }
            
            $servicio = '';
            if ($r['servicio_id']) {
                $servicioModel = new Servicio();
                $servicioData = $servicioModel->find($r['servicio_id']);
                $servicio = $servicioData ? $servicioData['nombre'] : 'Servicio no encontrado';
            }

            $data[] = array(
                esc($r['nombre']),
                esc($r['empresa'] ?? ''),
                esc(mb_substr($r['testimonio'], 0, 80)) . '...',
                $estrellas,
                $proyecto,
                $servicio,
                $estado,
                $destacado,
                '<button class="btn btn-sm btn-outline-primary" onclick="editarTestimonio(' . $r['id'] . ')">Editar</button> ' .
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarTestimonio(' . $r['id'] . ')">Eliminar</button>'
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $testimonio->countAll(),
            "recordsFiltered" => $testimonio->countAll(),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function editar($id)
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $testimonio = new Testimonio();
        $data['testimonio'] = $testimonio->find($id);
        
        if (!$data['testimonio']) {
            return redirect()->to(base_url('dashboard/testimonio/lista'))->with('error', 'Testimonio no encontrado');
        }

        // Obtener proyectos y servicios para los selects
        $proyectoModel = new Proyecto();
        $servicioModel = new Servicio();
        
        $data['proyectos'] = $proyectoModel->where('estado', 'A')->findAll();
        $data['servicios'] = $servicioModel->where('estado', 'A')->findAll();

        echo view('Modulos/testimonio/editar', $data);
    }

    public function update()
    {
        $testimonio = new Testimonio();
        $id = $this->request->getPost('id');

        if (!$this->validate('formTestimonioEdit')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'nombre', 'cargo', 'empresa', 'testimonio', 'calificacion',
            'proyecto_id', 'servicio_id', 'estado', 'destacado', 'fecha_proyecto'
        ]);

        // Generar slug y SEO
        $slug = $testimonio->generarSlug($post['nombre'], $id);
        $seo = $testimonio->generarSEO($post['nombre'], $post['testimonio'], $post['empresa']);

        $data = [
            'nombre' => $post['nombre'],
            'cargo' => $post['cargo'],
            'empresa' => $post['empresa'],
            'testimonio' => $post['testimonio'],
            'calificacion' => $post['calificacion'],
            'proyecto_id' => !empty($post['proyecto_id']) ? $post['proyecto_id'] : null,
            'servicio_id' => !empty($post['servicio_id']) ? $post['servicio_id'] : null,
            'estado' => $post['estado'],
            'destacado' => $post['destacado'],
            'fecha_proyecto' => !empty($post['fecha_proyecto']) ? $post['fecha_proyecto'] : null,
            'slug' => $slug,
            'meta_titulo' => $seo['meta_titulo'],
            'meta_descripcion' => $seo['meta_descripcion'],
            'meta_keywords' => $seo['meta_keywords']
        ];

        if ($testimonio->update($id, $data)) {
            return redirect()->to(base_url('dashboard/testimonio/lista'))->with('success', 'Testimonio actualizado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al actualizar el testimonio');
        }
    }

    public function eliminar($id)
    {
        $testimonio = new Testimonio();
        
        if ($testimonio->delete($id)) {
            return redirect()->to(base_url('dashboard/testimonio/lista'))->with('success', 'Testimonio eliminado con éxito');
        } else {
            return redirect()->back()->with('error', 'Error al eliminar el testimonio');
        }
    }
}
