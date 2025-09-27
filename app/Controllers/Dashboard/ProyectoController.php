<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\ModuloDetalle;
use App\Models\Proyecto;
use App\Models\Imagen;

class ProyectoController extends BaseController
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
        
        // Opciones para los selects
        $data['tipos_proyecto'] = [
            'residencial' => 'Residencial',
            'comercial' => 'Comercial',
            'industrial' => 'Industrial',
            'institucional' => 'Institucional',
            'otro' => 'Otro'
        ];
        
        $data['estados_proyecto'] = [
            'en_progreso' => 'En Progreso',
            'completado' => 'Completado',
            'en_pausa' => 'En Pausa',
            'cancelado' => 'Cancelado'
        ];

        echo view('Modulos/proyecto/registro', $data);
    }

    public function registrar()
    {
        $proyecto = new Proyecto();

        if (!$this->validate('formProyectoRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'nombre', 'cliente', 'tipo_proyecto', 'ubicacion', 'direccion',
            'fecha_inicio', 'fecha_finalizacion', 'presupuesto', 'mostrar_presupuesto',
            'estado', 'descripcion_corta', 'descripcion_detallada', 'caracteristicas_tecnicas',
            'area_construida', 'materiales_principales', 'testimonio_cliente', 'nombre_cliente',
            'destacado', 'meta_titulo', 'meta_descripcion', 'meta_keywords'
        ]);
        
        // Generar slug automáticamente
        $slug = $proyecto->generarSlug($post['nombre']);
        
        $data = [
            'nombre' => $post['nombre'],
            'slug' => $slug,
            'cliente' => $post['cliente'],
            'tipo_proyecto' => $post['tipo_proyecto'],
            'ubicacion' => $post['ubicacion'],
            'direccion' => $post['direccion'],
            'fecha_inicio' => $post['fecha_inicio'] ?: null,
            'fecha_finalizacion' => $post['fecha_finalizacion'] ?: null,
            'presupuesto' => $post['presupuesto'] ?: null,
            'mostrar_presupuesto' => $post['mostrar_presupuesto'] ?? false,
            'estado' => $post['estado'],
            'descripcion_corta' => $post['descripcion_corta'],
            'descripcion_detallada' => $post['descripcion_detallada'],
            'caracteristicas_tecnicas' => $post['caracteristicas_tecnicas'],
            'area_construida' => $post['area_construida'] ?: null,
            'materiales_principales' => $post['materiales_principales'],
            'testimonio_cliente' => $post['testimonio_cliente'],
            'nombre_cliente' => $post['nombre_cliente'],
            'destacado' => $post['destacado'] ?? false,
            'meta_titulo' => $post['meta_titulo'],
            'meta_descripcion' => $post['meta_descripcion'],
            'meta_keywords' => $post['meta_keywords'],
            'estado_publico' => 'A'
        ];

        if ($proyecto->insert($data)) {
            $proyectoId = $proyecto->getInsertID();
            
            // Procesar imágenes si se subieron
            $this->procesarImagenes($proyectoId);
            
            return redirect()->to(base_url('dashboard/proyecto/lista'))->with('success', 'Proyecto registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el proyecto');
        }
    }

    public function getProyecto()
    {
        $proyecto = new Proyecto();
        $imagen = new Imagen();
        $draw = intval($this->request->getGet("draw"));
        $proyectos = $proyecto->orderBy('fcreacion', 'DESC')->findAll();

        $data = array();
        foreach ($proyectos as $r) {
            // Obtener imagen portada
            $imagenPortada = $imagen->getImagenPortada('proyecto', $r->id);
            $imagenHtml = $imagenPortada ? 
                '<img src="' . base_url($imagenPortada->ruta) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">' : 
                '<span class="badge badge-secondary">Sin imagen</span>';

            $data[] = array(
                $r->nombre,
                ucfirst($r->tipo_proyecto),
                $r->ubicacion ?? 'No especificada',
                $r->estado == 'completado' ? '<span class="badge badge-success">Completado</span>' : 
                   ($r->estado == 'en_progreso' ? '<span class="badge badge-primary">En Progreso</span>' : 
                   ($r->estado == 'en_pausa' ? '<span class="badge badge-warning">En Pausa</span>' : 
                   '<span class="badge badge-danger">Cancelado</span>')),
                $r->destacado ? '<span class="badge badge-info">Destacado</span>' : '<span class="badge badge-light">Normal</span>',
                $imagenHtml,
                '<a href="editar/' . $r->id . '" style="display:inline-block; margin-right: 5px;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $proyecto->countAll(),
            "recordsFiltered" => $proyecto->countAll(),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function editar($id)
    {
        $proyecto = new Proyecto();
        $imagen = new Imagen();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['proyecto'] = $proyecto->find($id);
        $data['imagenes'] = $imagen->getImagenesPorEntidad('proyecto', $id);
        
        // Debug temporal
        log_message('info', 'Proyecto ID: ' . $id);
        log_message('info', 'Imágenes encontradas: ' . count($data['imagenes']));
        if (!empty($data['imagenes'])) {
            foreach ($data['imagenes'] as $img) {
                log_message('info', 'Imagen: ' . $img->ruta);
            }
        }
        
        // Opciones para los selects
        $data['tipos_proyecto'] = [
            'residencial' => 'Residencial',
            'comercial' => 'Comercial',
            'industrial' => 'Industrial',
            'institucional' => 'Institucional',
            'otro' => 'Otro'
        ];
        
        $data['estados_proyecto'] = [
            'en_progreso' => 'En Progreso',
            'completado' => 'Completado',
            'en_pausa' => 'En Pausa',
            'cancelado' => 'Cancelado'
        ];

        echo view("Modulos/proyecto/editar", $data);
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
        echo view('Modulos/proyecto/lista', $data);
    }

    public function eliminar()
    {
        $proyecto = new Proyecto();
        $imagen = new Imagen();
        $id = $this->request->getPost('id');
        
        // Eliminar imágenes asociadas
        $imagen->eliminarImagenesEntidad('proyecto', $id);
        
        if ($proyecto->delete($id)) {
            return redirect()->to(base_url('dashboard/proyecto/lista'))->with('success', 'Proyecto eliminado con éxito');
        } else {
            return redirect()->back()->with('errors', 'Error al eliminar el proyecto');
        }
    }

    public function update()
    {
        if (!$this->validate('formProyectoEdit')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $proyecto = new Proyecto();

        $id = $this->request->getPost('id');
        $post = $this->request->getPost([
            'nombre', 'cliente', 'tipo_proyecto', 'ubicacion', 'direccion',
            'fecha_inicio', 'fecha_finalizacion', 'presupuesto', 'mostrar_presupuesto',
            'estado', 'descripcion_corta', 'descripcion_detallada', 'caracteristicas_tecnicas',
            'area_construida', 'materiales_principales', 'testimonio_cliente', 'nombre_cliente',
            'destacado', 'meta_titulo', 'meta_descripcion', 'meta_keywords'
        ]);
        
        $arreglo = [
            'nombre' => $post['nombre'],
            'cliente' => $post['cliente'],
            'tipo_proyecto' => $post['tipo_proyecto'],
            'ubicacion' => $post['ubicacion'],
            'direccion' => $post['direccion'],
            'fecha_inicio' => $post['fecha_inicio'] ?: null,
            'fecha_finalizacion' => $post['fecha_finalizacion'] ?: null,
            'presupuesto' => $post['presupuesto'] ?: null,
            'mostrar_presupuesto' => $post['mostrar_presupuesto'] ?? false,
            'estado' => $post['estado'],
            'descripcion_corta' => $post['descripcion_corta'],
            'descripcion_detallada' => $post['descripcion_detallada'],
            'caracteristicas_tecnicas' => $post['caracteristicas_tecnicas'],
            'area_construida' => $post['area_construida'] ?: null,
            'materiales_principales' => $post['materiales_principales'],
            'testimonio_cliente' => $post['testimonio_cliente'],
            'nombre_cliente' => $post['nombre_cliente'],
            'destacado' => $post['destacado'] ?? false,
            'meta_titulo' => $post['meta_titulo'],
            'meta_descripcion' => $post['meta_descripcion'],
            'meta_keywords' => $post['meta_keywords']
        ];

        if ($proyecto->update($id, $arreglo)) {
            // Procesar nuevas imágenes si se subieron
            $this->procesarImagenes($id);
            
            return redirect()->to(base_url('dashboard/proyecto/editar/' . $id))->with('success', 'Proyecto editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el proyecto');
        }
    }

    /**
     * Procesar imágenes subidas
     */
    private function procesarImagenes($proyectoId)
    {
        $imagen = new Imagen();
        $files = $this->request->getFiles();
        
        if (isset($files['imagenes']) && !empty($files['imagenes'])) {
            $uploadPath = WRITEPATH . 'uploads/proyectos/';
            $publicPath = FCPATH . 'uploads/proyectos/';
            
            // Crear directorios si no existen
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            if (!is_dir($publicPath)) {
                mkdir($publicPath, 0755, true);
            }
            
            foreach ($files['imagenes'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    
                    // Mover a writable
                    $file->move($uploadPath, $newName);
                    
                    // Copiar a public para acceso web
                    copy($uploadPath . $newName, $publicPath . $newName);
                    
                    // Obtener siguiente orden
                    $orden = $imagen->getSiguienteOrden('proyecto', $proyectoId);
                    
                    $imagenData = [
                        'nombre_archivo' => $newName,
                        'ruta' => 'uploads/proyectos/' . $newName,
                        'tipo' => 'proyecto',
                        'entidad_id' => $proyectoId,
                        'es_portada' => false,
                        'orden' => $orden,
                        'descripcion' => '',
                        'estado' => 'A'
                    ];
                    
                    $imagen->insert($imagenData);
                }
            }
        }
    }

    /**
     * Establecer imagen como portada
     */
    public function setPortada()
    {
        $imagen = new Imagen();
        $imagenId = $this->request->getPost('imagen_id');
        $proyectoId = $this->request->getPost('proyecto_id');
        
        if ($imagen->setImagenPortada('proyecto', $proyectoId, $imagenId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Imagen establecida como portada']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al establecer portada']);
        }
    }

    /**
     * Eliminar imagen
     */
    public function eliminarImagen()
    {
        $imagen = new Imagen();
        $imagenId = $this->request->getPost('imagen_id');
        
        if ($imagen->delete($imagenId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Imagen eliminada']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al eliminar imagen']);
        }
    }
}
