<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\ModuloDetalle;
use App\Models\Proyecto;
use App\Models\Imagen;
use App\Models\Cliente;

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
            
            // Procesar imágenes si se subieron (sin establecer portada automáticamente)
            $this->procesarImagenes($proyectoId, false);
            
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
        
        // Obtener filtros
        $tipo_proyecto = $this->request->getGet('tipo_proyecto');
        $estado = $this->request->getGet('estado');
        $cliente = $this->request->getGet('cliente');
        
        // Construir consulta
        $query = $proyecto;
        
        if (!empty($tipo_proyecto)) {
            $query = $query->where('tipo_proyecto', $tipo_proyecto);
        }
        
        if (!empty($estado)) {
            $query = $query->where('estado', $estado);
        }
        
        if (!empty($cliente)) {
            $query = $query->where('cliente', $cliente);
        }
        
        $proyectos = $query->orderBy('fcreacion', 'DESC')->findAll();

        $data = array();
        foreach ($proyectos as $r) {
            log_message('debug', 'Proyecto destacado: ' . ($r->destacado ?? 'NULL') . ' - Tipo: ' . gettype($r->destacado));
            // Obtener imagen portada
            $imagenPortada = $imagen->getImagenPortada('proyecto', $r->id);
            $imagenHtml = $imagenPortada ? 
                '<img src="' . base_url($imagenPortada->ruta) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">' : 
                '<span class="badge badge-secondary">Sin imagen</span>';

            $data[] = array(
                $imagenHtml,
                $r->nombre,
                $r->cliente ?? 'No especificado',
                ucfirst($r->tipo_proyecto),
                $r->estado == 'completado' ? '<span class="badge badge-success">Completado</span>' : 
                   ($r->estado == 'en_progreso' ? '<span class="badge badge-primary">En Progreso</span>' : 
                   ($r->estado == 'en_pausa' ? '<span class="badge badge-warning">En Pausa</span>' : 
                   '<span class="badge badge-danger">Cancelado</span>')),
                $r->ubicacion ?? 'No especificada',
                $r->presupuesto ? '$' . number_format($r->presupuesto, 0, ',', '.') : 'No especificado',
                !empty($r->destacado) && ($r->destacado == 'S' || $r->destacado == 1) ? '<span class="badge badge-info">Destacado</span>' : '<span class="badge badge-secondary">Normal</span>',
                '<a href="editar/' . $r->id . '" style="display:inline-block; margin-right: 5px;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }
        // Contar registros filtrados
        $queryCount = new Proyecto();
        
        if (!empty($tipo_proyecto)) {
            $queryCount = $queryCount->where('tipo_proyecto', $tipo_proyecto);
        }
        
        if (!empty($estado)) {
            $queryCount = $queryCount->where('estado', $estado);
        }
        
        if (!empty($cliente)) {
            $queryCount = $queryCount->where('cliente', $cliente);
        }
        
        $recordsFiltered = $queryCount->countAllResults(false);
        
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $proyecto->countAll(),
            "recordsFiltered" => $recordsFiltered,
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
            return redirect()->back()->with('error', 'Error al eliminar el proyecto');
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
            'destacado', 'meta_titulo', 'meta_descripcion', 'meta_keywords', 'estado_publico'
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
            'destacado' => $post['destacado'] ?? 0,
            'meta_titulo' => $post['meta_titulo'],
            'meta_descripcion' => $post['meta_descripcion'],
            'meta_keywords' => $post['meta_keywords'],
            'estado_publico' => $post['estado_publico'] ?? 'A'
        ];

        if ($proyecto->update($id, $arreglo)) {
            // Procesar nuevas imágenes si se subieron (sin establecer portada automáticamente)
            $this->procesarImagenes($id, false);
            
            return redirect()->to(base_url('dashboard/proyecto/lista'))->with('success', 'Proyecto editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al editar el proyecto');
        }
    }

    /**
     * Procesar imágenes subidas
     */
    private function procesarImagenes($proyectoId, $establecerPortada = false)
    {
        $imagen = new Imagen();
        $files = $this->request->getFiles();
        
        log_message('debug', 'Procesando imágenes para proyecto ID: ' . $proyectoId . ' - Establecer portada: ' . ($establecerPortada ? 'Sí' : 'No'));
        log_message('debug', 'Archivos recibidos: ' . json_encode(array_keys($files)));
        
        if (isset($files['imagenes']) && !empty($files['imagenes'])) {
            log_message('debug', 'Se encontraron ' . count($files['imagenes']) . ' imágenes para procesar');
            $uploadPath = WRITEPATH . 'uploads/proyectos/';
            $publicPath = FCPATH . 'uploads/proyectos/';
            
            // Crear directorios si no existen
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            if (!is_dir($publicPath)) {
                mkdir($publicPath, 0755, true);
            }
            
            // Determinar si se debe establecer portada automáticamente
            $primeraImagen = false; // Por defecto, no establecer portada
            
            if ($establecerPortada) {
                // Solo establecer portada si se especifica y no hay portada existente
                $portadaExistente = $imagen->getImagenPortada('proyecto', $proyectoId);
                $primeraImagen = !$portadaExistente;
            }
            
            foreach ($files['imagenes'] as $index => $file) {
                log_message('debug', "Procesando imagen $index: " . $file->getName() . " - Válida: " . ($file->isValid() ? 'Sí' : 'No') . " - Movida: " . ($file->hasMoved() ? 'Sí' : 'No'));
                
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    
                    // Mover a writable
                    if ($file->move($uploadPath, $newName)) {
                        log_message('debug', "Imagen movida exitosamente: $newName");
                        
                        // Copiar a public para acceso web
                        if (copy($uploadPath . $newName, $publicPath . $newName)) {
                            log_message('debug', "Imagen copiada a directorio público: $newName");
                            
                            // Obtener siguiente orden
                            $orden = $imagen->getSiguienteOrden('proyecto', $proyectoId);
                            
                            $imagenData = [
                                'nombre_archivo' => $newName,
                                'ruta' => 'uploads/proyectos/' . $newName,
                                'tipo' => 'proyecto',
                                'entidad_id' => $proyectoId,
                                'es_portada' => $primeraImagen, // Solo la primera será portada si no hay portada existente
                                'orden' => $orden,
                                'descripcion' => '',
                                'estado' => 'A'
                            ];
                            
                            if ($imagen->insert($imagenData)) {
                                log_message('debug', "Imagen insertada en BD exitosamente: $newName");
                            } else {
                                log_message('error', "Error al insertar imagen en BD: " . json_encode($imagen->errors()));
                            }
                            
                            $primeraImagen = false; // Las siguientes no son portada
                        } else {
                            log_message('error', "Error al copiar imagen a directorio público: $newName");
                        }
                    } else {
                        log_message('error', "Error al mover imagen: " . $file->getErrorString());
                    }
                } else {
                    log_message('debug', "Imagen $index no válida o ya movida: " . $file->getErrorString());
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

    /**
     * Obtener lista de clientes únicos de proyectos para el filtro
     */
    public function getClientesSelect()
    {
        // Verificar autenticación
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $proyecto = new Proyecto();
        
        // Obtener todos los clientes únicos de los proyectos
        $clientes = $proyecto->select('cliente')
                            ->where('cliente IS NOT NULL')
                            ->where('cliente !=', '')
                            ->distinct()
                            ->orderBy('cliente', 'ASC')
                            ->findAll();

        $data = [];
        foreach ($clientes as $c) {
            if (!empty($c->cliente)) {
                $data[] = [
                    'nombre' => $c->cliente
                ];
            }
        }

        return $this->response->setJSON($data);
    }
}
