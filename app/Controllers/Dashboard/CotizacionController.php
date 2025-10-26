<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Cotizacion;
use App\Models\CotizacionItem;
use App\Models\CotizacionArchivo;
use App\Models\Cliente;
use App\Models\ModuloDetalle;
use App\Traits\MaintainsFilters;

class CotizacionController extends BaseController
{
    use MaintainsFilters;

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

        return view('Modulos/cotizacion/lista', $data);
    }

    public function getCotizaciones()
    {
        // Verificar autenticación
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $cotizacion = new Cotizacion();
        $draw = intval($this->request->getGet("draw"));
        
        // Obtener filtros
        $estado = $this->request->getGet('estado');
        $cliente_id = $this->request->getGet('cliente_id');
        $busqueda = $this->request->getGet('busqueda');
        
        // Construir consulta con JOIN
        $query = $cotizacion->select('cotizaciones.*, c.nombre_razon_social as cliente_nombre, c.tipo_cliente')
                          ->join('clientes c', 'c.id = cotizaciones.cliente_id', 'left');
        
        if (!empty($estado)) {
            $query->where('cotizaciones.estado', $estado);
        }
        
        if (!empty($cliente_id)) {
            $query->where('cotizaciones.cliente_id', $cliente_id);
        }
        
        if (!empty($busqueda)) {
            $query->groupStart()
                  ->like('cotizaciones.numero_cotizacion', $busqueda)
                  ->orLike('cotizaciones.proyecto_nombre', $busqueda)
                  ->orLike('c.nombre_razon_social', $busqueda)
                  ->groupEnd();
        }
        
        $rows = $query->orderBy('cotizaciones.fecha_cotizacion', 'DESC')
                     ->findAll();

        $data = array();
        foreach ($rows as $r) {
            $estadoBadge = match ($r->estado ?? 'borrador') {
                'borrador' => '<span class="badge badge-secondary">Borrador</span>',
                'enviada' => '<span class="badge badge-info">Enviada</span>',
                'revisada' => '<span class="badge badge-warning">Revisada</span>',
                'aprobada' => '<span class="badge badge-success">Aprobada</span>',
                'rechazada' => '<span class="badge badge-danger">Rechazada</span>',
                'expirada' => '<span class="badge badge-warning">Expirada</span>',
                default => '<span class="badge badge-secondary">N/A</span>',
            };

            $prioridadBadge = match ($r->prioridad) {
                'baja' => '<span class="badge badge-success">Baja</span>',
                'media' => '<span class="badge badge-warning">Media</span>',
                'alta' => '<span class="badge badge-danger">Alta</span>',
                'urgente' => '<span class="badge badge-danger">Urgente</span>',
                default => '<span class="badge badge-secondary">N/A</span>',
            };

            $fecha = 'N/A';
            if (!empty($r->fecha_cotizacion) && $r->fecha_cotizacion !== '0000-00-00') {
                try {
                    $fecha = date('d/m/Y', strtotime($r->fecha_cotizacion));
                } catch (Exception $e) {
                    $fecha = 'Fecha inválida';
                }
            }

            $data[] = array(
                esc($r->numero_cotizacion),
                esc($r->proyecto_nombre ?? $r->titulo ?? 'Sin título'),
                esc($r->cliente_nombre),
                $estadoBadge,
                $prioridadBadge,
                '$' . number_format($r->total_general, 0, ',', '.'),
                $fecha,
                '<button class="btn btn-sm btn-outline-primary" onclick="editarCotizacion(' . $r->id . ')">Editar</button> ' .
                '<button class="btn btn-sm btn-outline-info" onclick="verDetalleCotizacion(' . $r->id . ')">Ver</button> ' .
                '<button class="btn btn-sm btn-outline-success" onclick="generarPDF(' . $r->id . ')">PDF</button> ' .
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarCotizacion(' . $r->id . ')">Eliminar</button>'
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $cotizacion->countAllResults(),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        return $this->response->setJSON($output);
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

        // Obtener clientes activos
        $cliente = new Cliente();
        $data['clientes'] = $cliente->getClientesActivos();
        
        // Obtener cliente preseleccionado si viene en la URL
        $clienteId = $this->request->getGet('cliente_id');
        if ($clienteId) {
            $data['clienteSeleccionado'] = $cliente->find($clienteId);
        } else {
            $data['clienteSeleccionado'] = null;
        }

        return view('Modulos/cotizacion/registro', $data);
    }

    public function registrar()
    {
        $cotizacion = new Cotizacion();

        if (!$this->validate('formCotizacionRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'cliente_id', 'titulo', 'descripcion', 'fecha_cotizacion', 'fecha_validez',
            'estado_cotizacion', 'prioridad', 'descuento_porcentaje', 'descuento_monto',
            'subtotal', 'iva_porcentaje', 'iva_monto', 'total_general', 'condiciones_pago', 'observaciones',
            'proyecto_tipo', 'proyecto_area', 'proyecto_ubicacion', 'proyecto_direccion'
        ]);

        // Generar número de cotización
        $numero_cotizacion = $cotizacion->generarNumeroCotizacion();

        // Generar slug
        $slug = $cotizacion->generarSlug($post['titulo']);

        // Calcular vigencia en días
        $fechaInicio = new \DateTime($post['fecha_cotizacion']);
        $fechaFin = new \DateTime($post['fecha_validez']);
        $vigenciaDias = $fechaInicio->diff($fechaFin)->days;

        // Calcular subtotales por tipo de item
        $subtotalMateriales = 0;
        $subtotalManoObra = 0;
        $subtotalServicios = 0;
        $subtotalEquipos = 0;
        $subtotalOtros = 0;

        $items = $this->request->getPost('items');
        if ($items && is_array($items)) {
            foreach ($items as $item) {
                if (isset($item['cantidad']) && isset($item['precio_unitario'])) {
                    $cantidad = floatval($item['cantidad']);
                    $precio = floatval(str_replace(['.', ','], ['', '.'], $item['precio_unitario']));
                    $subtotal = $cantidad * $precio;
                    
                    $tipoItem = $item['categoria'] ?? 'otros';
                    
                    switch ($tipoItem) {
                        case 'material':
                            $subtotalMateriales += $subtotal;
                            break;
                        case 'mano_obra':
                            $subtotalManoObra += $subtotal;
                            break;
                        case 'servicio':
                            $subtotalServicios += $subtotal;
                            break;
                        case 'equipo':
                            $subtotalEquipos += $subtotal;
                            break;
                        default:
                            $subtotalOtros += $subtotal;
                            break;
                    }
                }
            }
        }

        $data = [
            'cliente_id' => $post['cliente_id'],
            'numero_cotizacion' => $numero_cotizacion,
            'proyecto_nombre' => $post['titulo'], // Mapear titulo a proyecto_nombre
            'proyecto_descripcion' => $post['descripcion'] ?? null,
            'proyecto_tipo' => $post['proyecto_tipo'] ?? 'residencial',
            'proyecto_area' => $post['proyecto_area'] ?? null,
            'proyecto_ubicacion' => $post['proyecto_ubicacion'] ?? null,
            'proyecto_direccion' => $post['proyecto_direccion'] ?? null,
            'fecha_cotizacion' => $post['fecha_cotizacion'],
            'fecha_validez' => $post['fecha_validez'] ?? null,
            'vigencia_dias' => $vigenciaDias,
            'estado' => $post['estado_cotizacion'] ?? 'borrador',
            'prioridad' => $post['prioridad'],
            'subtotal_materiales' => $subtotalMateriales,
            'subtotal_mano_obra' => $subtotalManoObra,
            'subtotal_servicios' => $subtotalServicios,
            'descuento_porcentaje' => $post['descuento_porcentaje'] ?? 0,
            'descuento_monto' => $post['descuento_monto'] ?? 0,
            'subtotal_sin_iva' => $post['subtotal'] ?? 0,
            'iva_porcentaje' => $post['iva_porcentaje'] ?? 19,
            'iva_monto' => $post['iva_monto'] ?? 0,
            'total_general' => $post['total_general'] ?? 0,
            'forma_pago' => 'contado', // Valor por defecto
            'plazo_pago_dias' => 0,
            'anticipo_porcentaje' => 0,
            'anticipo_monto' => 0,
            'tiempo_ejecucion_dias' => null,
            'fecha_inicio_estimada' => null,
            'fecha_fin_estimada' => null,
            'condiciones_generales' => $post['condiciones_pago'] ?? null,
            'observaciones_especiales' => $post['observaciones'] ?? null,
            'garantia_meses' => 12, // Valor por defecto
            'slug' => $slug,
            'meta_titulo' => null,
            'meta_descripcion' => null,
            'creado_por' => session()->get('usuario')['id'] ?? null
        ];

        if ($cotizacion->insert($data)) {
            $cotizacionId = $cotizacion->getInsertID();
            
            // Guardar items de la cotización
            if ($items && is_array($items)) {
                $cotizacionItem = new CotizacionItem();
                $orden = 1;
                
                foreach ($items as $item) {
                    if (isset($item['descripcion']) && !empty($item['descripcion'])) {
                        $itemData = [
                            'cotizacion_id' => $cotizacionId,
                            'categoria' => $item['categoria'] ?? 'otros',
                            'subcategoria' => $item['subcategoria'] ?? '',
                            'descripcion' => $item['descripcion'],
                            'cantidad' => floatval($item['cantidad'] ?? 1),
                            'unidad' => $item['unidad'] ?? '',
                            'precio_unitario' => floatval(str_replace(['.', ','], ['', '.'], $item['precio_unitario'] ?? 0)),
                            'subtotal' => floatval($item['cantidad'] ?? 1) * floatval(str_replace(['.', ','], ['', '.'], $item['precio_unitario'] ?? 0)),
                            'es_opcional' => intval($item['es_opcional'] ?? 0),
                            'orden' => $orden++
                        ];
                        
                        $cotizacionItem->insert($itemData);
                    }
                }
            }
            
            return redirect()->to(base_url('dashboard/cotizacion/lista'))->with('success', 'Cotización registrada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar la cotización');
        }
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

        $cotizacion = new Cotizacion();
        $data['cotizacion'] = $cotizacion->find($id);
        
        if (!$data['cotizacion']) {
            return redirect()->to(base_url('dashboard/cotizacion/lista'))->with('error', 'Cotización no encontrada');
        }

        // Obtener cliente
        $cliente = new Cliente();
        $data['cliente'] = $cliente->find($data['cotizacion']->cliente_id);
        
        // Obtener todos los clientes para el select
        $data['clientes'] = $cliente->getClientesActivos();

        // Obtener items de la cotización
        $cotizacionItem = new CotizacionItem();
        $data['items'] = $cotizacionItem->where('cotizacion_id', $id)
                                      ->orderBy('orden', 'ASC')
                                      ->findAll();

        // Obtener archivos de la cotización
        $cotizacionArchivo = new CotizacionArchivo();
        $data['archivos'] = $cotizacionArchivo->where('cotizacion_id', $id)
                                             ->orderBy('es_principal', 'DESC')
                                             ->orderBy('fcreacion', 'ASC')
                                             ->findAll();

        return view('Modulos/cotizacion/editar', $data);
    }

    public function update()
    {
        $cotizacion = new Cotizacion();
        $id = $this->request->getPost('id');

        if (!$this->validate('formCotizacionEdit')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'cliente_id', 'titulo', 'descripcion', 'fecha_cotizacion', 'fecha_validez',
            'estado_cotizacion', 'prioridad', 'descuento_porcentaje', 'descuento_monto',
            'subtotal', 'iva_porcentaje', 'iva_monto', 'total_general', 'condiciones_pago', 'observaciones',
            'proyecto_tipo', 'proyecto_area', 'proyecto_ubicacion', 'proyecto_direccion'
        ]);

        // Debug: Log del estado recibido
        log_message('debug', 'Estado cotización recibido: ' . ($post['estado_cotizacion'] ?? 'NO RECIBIDO'));

        // Generar slug
        $slug = $cotizacion->generarSlug($post['titulo'], $id);

        $data = [
            'cliente_id' => $post['cliente_id'],
            'proyecto_nombre' => $post['titulo'], // Mapear titulo a proyecto_nombre
            'proyecto_descripcion' => $post['descripcion'] ?? null, // Mapear descripcion a proyecto_descripcion
            'fecha_cotizacion' => $post['fecha_cotizacion'],
            'fecha_validez' => $post['fecha_validez'] ?? null,
            'estado' => $post['estado_cotizacion'] ?? 'borrador', // Mapear estado_cotizacion a estado
            'prioridad' => $post['prioridad'],
            'descuento_porcentaje' => $post['descuento_porcentaje'] ?? 0,
            'descuento_monto' => $post['descuento_monto'] ?? 0,
            'subtotal_sin_iva' => $post['subtotal'] ?? 0, // Mapear subtotal a subtotal_sin_iva
            'iva_porcentaje' => $post['iva_porcentaje'] ?? 19,
            'iva_monto' => $post['iva_monto'] ?? 0,
            'total_general' => $post['total_general'] ?? 0,
            'condiciones_generales' => $post['condiciones_pago'] ?? null, // Mapear condiciones_pago a condiciones_generales
            'observaciones_especiales' => $post['observaciones'] ?? null, // Mapear observaciones a observaciones_especiales
            'proyecto_tipo' => $post['proyecto_tipo'] ?? 'residencial',
            'proyecto_area' => $post['proyecto_area'] ?? null,
            'proyecto_ubicacion' => $post['proyecto_ubicacion'] ?? null,
            'proyecto_direccion' => $post['proyecto_direccion'] ?? null,
            'slug' => $slug
        ];

        // Debug: Log de los datos que se van a actualizar
        log_message('debug', 'Datos a actualizar: ' . json_encode($data));
        
        if ($cotizacion->update($id, $data)) {
            // Eliminar items existentes
            $cotizacionItem = new CotizacionItem();
            $cotizacionItem->where('cotizacion_id', $id)->delete();
            
            // Guardar nuevos items
            $items = $this->request->getPost('items');
            if ($items && is_array($items)) {
                $orden = 1;
                
                foreach ($items as $item) {
                    if (isset($item['descripcion']) && !empty($item['descripcion'])) {
                        $itemData = [
                            'cotizacion_id' => $id,
                            'categoria' => $item['categoria'] ?? 'otros',
                            'subcategoria' => $item['subcategoria'] ?? '',
                            'descripcion' => $item['descripcion'],
                            'cantidad' => floatval($item['cantidad'] ?? 1),
                            'unidad' => $item['unidad'] ?? '',
                            'precio_unitario' => floatval(str_replace(['.', ','], ['', '.'], $item['precio_unitario'] ?? 0)),
                            'subtotal' => floatval($item['cantidad'] ?? 1) * floatval(str_replace(['.', ','], ['', '.'], $item['precio_unitario'] ?? 0)),
                            'es_opcional' => intval($item['es_opcional'] ?? 0),
                            'orden' => $orden++
                        ];
                        
                        $cotizacionItem->insert($itemData);
                    }
                }
            }
            
            return redirect()->to(base_url('dashboard/cotizacion/lista'))->with('success', 'Cotización actualizada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al actualizar la cotización');
        }
    }

    public function eliminar($id)
    {
        $cotizacion = new Cotizacion();
        
        // Eliminar items asociados
        $cotizacionItem = new CotizacionItem();
        $cotizacionItem->where('cotizacion_id', $id)->delete();
        
        // Eliminar archivos asociados
        $cotizacionArchivo = new CotizacionArchivo();
        $archivos = $cotizacionArchivo->where('cotizacion_id', $id)->findAll();
        foreach ($archivos as $archivo) {
            if (file_exists($archivo->ruta_archivo)) {
                unlink($archivo->ruta_archivo);
            }
        }
        $cotizacionArchivo->where('cotizacion_id', $id)->delete();
        
        if ($cotizacion->delete($id)) {
            return redirect()->to(base_url('dashboard/cotizacion/lista'))->with('success', 'Cotización eliminada con éxito');
        } else {
            return redirect()->back()->with('error', 'Error al eliminar la cotización');
        }
    }

    public function detalle($id)
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $cotizacion = new Cotizacion();
        $data['cotizacion'] = $cotizacion->getCotizacionCompleta($id);
        
        if (!$data['cotizacion']) {
            return redirect()->to(base_url('dashboard/cotizacion/lista'))->with('error', 'Cotización no encontrada');
        }

        return view('Modulos/cotizacion/detalle', $data);
    }

    public function generarPDF($id)
    {
        log_message('debug', 'Iniciando generación de PDF para cotización ID: ' . $id);
        
        $cotizacion = new Cotizacion();
        $cotizacionData = $cotizacion->getCotizacionCompleta($id);
        
        log_message('debug', 'Datos de cotización obtenidos: ' . json_encode($cotizacionData ? 'OK' : 'NULL'));
        
        if (!$cotizacionData) {
            log_message('error', 'Cotización no encontrada para ID: ' . $id);
            return redirect()->back()->with('error', 'Cotización no encontrada');
        }

        log_message('debug', 'Número de cotización: ' . ($cotizacionData->numero_cotizacion ?? 'Sin número'));
        log_message('debug', 'Items encontrados: ' . count($cotizacionData->items ?? []));

        try {
            log_message('debug', 'Creando instancia de PDFGenerator');
            $pdfGenerator = new \App\Libraries\PDFGenerator();
            
            log_message('debug', 'Generando contenido PDF');
            $pdfContent = $pdfGenerator->generarCotizacionPDF(
                $cotizacionData, 
                $cotizacionData->items ?? [], 
                $cotizacionData->archivos ?? []
            );
            
            log_message('debug', 'PDF generado exitosamente, tamaño: ' . strlen($pdfContent) . ' bytes');
            
            $filename = 'Cotizacion_' . ($cotizacionData->numero_cotizacion ?? 'SinNumero') . '.pdf';
            log_message('debug', 'Nombre del archivo: ' . $filename);
            
            return $pdfGenerator->descargarPDF($filename, $pdfContent);
            
        } catch (\Exception $e) {
            log_message('error', 'Error generando PDF: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    /**
     * Convertir cotización en proyecto
     */
    public function convertirEnProyecto($id)
    {
        log_message('debug', 'Método convertirEnProyecto llamado con ID: ' . $id);
        
        try {
            $cotizacion = new Cotizacion();
            $cotizacionData = $cotizacion->getCotizacionCompleta($id);
            
            log_message('debug', 'Datos de cotización obtenidos: ' . ($cotizacionData ? 'OK' : 'NULL'));
            
            if (!$cotizacionData) {
                return redirect()->to(base_url('dashboard/cotizacion/lista'))->with('error', 'Cotización no encontrada');
            }
            
            // Verificar que la cotización esté en estado apropiado para convertir
            if ($cotizacionData->estado !== 'aprobada') {
                return redirect()->to(base_url('dashboard/cotizacion/detalle/' . $id))->with('error', 'Solo se pueden convertir en proyecto las cotizaciones que estén en estado "Aprobada"');
            }
            
            $proyecto = new \App\Models\Proyecto();
            
            // Mapear datos de cotización a proyecto
            $proyectoData = [
                'nombre' => $cotizacionData->proyecto_nombre ?? $cotizacionData->titulo ?? 'Proyecto ' . $cotizacionData->numero_cotizacion,
                'slug' => $proyecto->generarSlug($cotizacionData->proyecto_nombre ?? $cotizacionData->titulo ?? 'Proyecto ' . $cotizacionData->numero_cotizacion),
                'cliente' => $cotizacionData->cliente_nombre ?? 'Cliente',
                'tipo_proyecto' => $this->mapearTipoProyecto($cotizacionData->proyecto_tipo ?? 'residencial'),
                'ubicacion' => $cotizacionData->proyecto_ubicacion ?? '',
                'direccion' => $cotizacionData->proyecto_direccion ?? '',
                'fecha_inicio' => date('Y-m-d'), // Fecha actual como inicio
                'fecha_finalizacion' => null, // Se puede calcular después
                'presupuesto' => $cotizacionData->total_general ?? 0,
                'mostrar_presupuesto' => false, // Por defecto no mostrar
                'estado' => 'en_progreso',
                'descripcion_corta' => substr($cotizacionData->proyecto_descripcion ?? '', 0, 500),
                'descripcion_detallada' => $cotizacionData->proyecto_descripcion ?? '',
                'caracteristicas_tecnicas' => $this->generarCaracteristicasTecnicas($cotizacionData),
                'area_construida' => $cotizacionData->proyecto_area ?? null,
                'materiales_principales' => $this->generarMaterialesPrincipales($cotizacionData),
                'testimonio_cliente' => null,
                'nombre_cliente' => $cotizacionData->cliente_nombre ?? '',
                'destacado' => false,
                'meta_titulo' => ($cotizacionData->proyecto_nombre ?? $cotizacionData->titulo ?? 'Proyecto') . ' - MANSANCHEZ',
                'meta_descripcion' => substr($cotizacionData->proyecto_descripcion ?? '', 0, 500),
                'meta_keywords' => 'proyecto, construcción, ' . strtolower($cotizacionData->proyecto_tipo ?? 'residencial'),
                'estado_publico' => 'A'
            ];
            
            // Insertar proyecto
            if ($proyecto->insert($proyectoData)) {
                $proyectoId = $proyecto->getInsertID();
                
                // Actualizar estado de la cotización a "aprobada"
                $cotizacion->update($id, [
                    'estado' => 'aprobada'
                ]);
                
                return redirect()->to(base_url('dashboard/cotizacion/detalle/' . $id))->with('success', 'Cotización convertida en proyecto exitosamente. <a href="' . base_url('dashboard/proyecto/editar/' . $proyectoId) . '">Ver proyecto</a>');
            } else {
                return redirect()->to(base_url('dashboard/cotizacion/detalle/' . $id))->with('error', 'Error al crear el proyecto');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error al convertir cotización en proyecto: ' . $e->getMessage());
            return redirect()->to(base_url('dashboard/cotizacion/detalle/' . $id))->with('error', 'Error al convertir la cotización en proyecto');
        }
    }
    
    /**
     * Mapear tipo de proyecto de cotización a proyecto
     */
    private function mapearTipoProyecto($tipoCotizacion)
    {
        $mapeo = [
            'residencial' => 'residencial',
            'comercial' => 'comercial',
            'industrial' => 'industrial',
            'institucional' => 'institucional',
            'infraestructura' => 'industrial',
            'mantenimiento' => 'comercial',
            'reparacion' => 'residencial',
            'otros' => 'otro'
        ];
        
        return $mapeo[$tipoCotizacion] ?? 'residencial';
    }
    
    /**
     * Generar características técnicas basadas en los items de la cotización
     */
    private function generarCaracteristicasTecnicas($cotizacionData)
    {
        if (empty($cotizacionData->items)) {
            return '';
        }
        
        $caracteristicas = [];
        foreach ($cotizacionData->items as $item) {
            if (!empty($item->especificaciones)) {
                $caracteristicas[] = $item->especificaciones;
            }
        }
        
        return implode(', ', $caracteristicas);
    }
    
    /**
     * Generar lista de materiales principales basada en los items
     */
    private function generarMaterialesPrincipales($cotizacionData)
    {
        if (empty($cotizacionData->items)) {
            return '';
        }
        
        $materiales = [];
        foreach ($cotizacionData->items as $item) {
            if ($item->categoria === 'material' && !empty($item->descripcion)) {
                $materiales[] = $item->descripcion;
            }
        }
        
        return implode(', ', array_slice($materiales, 0, 5)); // Máximo 5 materiales
    }

    public function getClientesSelect()
    {
        // Verificar autenticación
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $cliente = new Cliente();
        $clientes = $cliente->getClientesActivos();

        $data = [];
        foreach ($clientes as $c) {
            $data[] = [
                'id' => $c->id,
                'text' => $c->display_name,
                'tipo' => $c->tipo_cliente,
                'contacto' => $c->contacto_completo
            ];
        }

        return $this->response->setJSON($data);
    }
}
