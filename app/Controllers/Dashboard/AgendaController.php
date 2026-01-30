<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\AgendaPaciente;
use App\Models\Paciente;
use App\Models\HistorialClinico;
use App\Models\MetodoCalculo;
use App\Models\ModuloDetalle;
use App\Models\BotonPagoPlantilla;
use App\Models\EmpresaConfiguracion;
use App\Traits\MaintainsFilters;
use App\Libraries\WhatsAppService;
use App\Libraries\CalendarService;
use Config\Services;

class AgendaController extends BaseController
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

        return view('Modulos/agenda/lista', $data);
    }

    public function gestionar()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        return view('Modulos/agenda/gestionar', $data);
    }

    public function calendario()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        // Cargar pacientes para el select
        $pacienteModel = new Paciente();
        $data['pacientes'] = $pacienteModel->getPacientesSelect();

        // Cargar modalidades para el select
        $db = \Config\Database::connect();
        $data['modalidades'] = $db->table('modalidad_agenda')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();

        // Cargar plantillas de botones de pago (solo si Mercado Pago está habilitado)
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        $data['plantillas_pago'] = [];
        
        if ($empresaId) {
            $empresaConfigModel = new EmpresaConfiguracion();
            if ($empresaConfigModel->mercadoPagoHabilitado($empresaId)) {
                $plantillaModel = new BotonPagoPlantilla();
                $data['plantillas_pago'] = $plantillaModel->getPlantillasActivas($empresaId);
            }
        }

        return view('Modulos/agenda/calendario', $data);
    }

    public function getEventos()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $nutricionista_id = $this->request->getGet('nutricionista_id');
        
        // Si no se proporciona nutricionista_id, usar el usuario de la sesión
        if (!$nutricionista_id) {
            $nutricionista_id = session()->get('usuario')['id'];
        }

        $db = \Config\Database::connect();
        
        // Obtener eventos desde detalle_agenda (los datos del paciente están directamente en detalle_agenda)
        $builder = $db->table('detalle_agenda da')
            ->select('da.id, a.fecha, da.hora_inicio, da.hora_fin, da.modalidad_id, da.paciente_id, da.estado_cita, da.tipo_consulta, da.motivo, da.observaciones, da.fecha_confirmacion, da.fecha_cancelacion, da.motivo_cancelacion, p.nombre, p.apellido, p.telefono, p.email, p.rut_dni')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            // Convertir fechas para comparación (start y end vienen en YYYY-MM-DD, fecha está en DD-MM-YYYY)
            ->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') >= ", $start)
            ->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') <= ", $end)
            ->where('da.usuario_id', $nutricionista_id);
            // Mostrar todas las citas incluyendo canceladas (se mostrarán en rojo)

        $eventos = $builder->get()->getResult();
        
        // Obtener la hora mínima del horario del usuario para configurar slotMinTime
        $horaMinima = $db->table('agenda a')
            ->select('MIN(a.hora_inicio) as hora_minima')
            ->join('detalle_agenda da', 'da.agenda_id = a.id', 'inner')
            ->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') >= ", $start)
            ->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') <= ", $end)
            ->where('da.usuario_id', $nutricionista_id)
            ->get()
            ->getRow();
        
        // Si no hay eventos en el rango, buscar la hora mínima global del usuario
        if (!$horaMinima || !$horaMinima->hora_minima) {
            $horaMinima = $db->table('agenda a')
                ->select('MIN(a.hora_inicio) as hora_minima')
                ->join('detalle_agenda da', 'da.agenda_id = a.id', 'inner')
                ->where('da.usuario_id', $nutricionista_id)
                ->get()
                ->getRow();
        }
        
        $horaMinimaStr = $horaMinima && $horaMinima->hora_minima ? $horaMinima->hora_minima : '09:00:00';
        // FullCalendar espera formato HH:MM:SS, así que mantenemos el formato completo
        $horaMinimaFormateada = $horaMinimaStr; // Ya viene en formato HH:MM:SS desde la BD

        $data = [];
        foreach ($eventos as $evento) {
            // Convertir fecha de DD-MM-YYYY a YYYY-MM-DD para FullCalendar
            $fechaParaCalendar = $evento->fecha;
            if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $evento->fecha, $matches)) {
                $fechaParaCalendar = $matches[3] . '-' . $matches[2] . '-' . $matches[1]; // Convertir a YYYY-MM-DD
            }
            $fechaInicio = $fechaParaCalendar . 'T' . $evento->hora_inicio;
            $fechaFin = $fechaParaCalendar . 'T' . $evento->hora_fin;
            
            $nombrePaciente = $evento->nombre ? trim(($evento->nombre ?? '') . ' ' . ($evento->apellido ?? '')) : 'Disponible';
            
            // Determinar si está disponible (sin paciente asignado)
            $estaDisponible = empty($evento->paciente_id);
            $modalidadId = (int)($evento->modalidad_id ?? 3);
            
            // Determinar el estado de la cita
            // REGLA PRINCIPAL: Si no tiene paciente asignado, SIEMPRE está disponible (verde)
            // Si tiene paciente, usar estado_cita o 'pendiente' por defecto
            if ($estaDisponible) {
                // Sin paciente = Disponible (verde)
                $estadoCita = 'disponible';
            } elseif (!empty($evento->estado_cita) && $evento->estado_cita !== 'NULL') {
                // Tiene paciente y tiene estado_cita explícito, usarlo
                $estadoCita = $evento->estado_cita;
            } else {
                // Tiene paciente pero no tiene estado_cita, está pendiente (naranja)
                $estadoCita = 'pendiente';
            }
            
            // Paleta de colores profesional para salud (COLOR = ESTADO únicamente)
            // Diseñada para ser accesible, transmitir calma y profesionalismo
            // Optimizada para uso prolongado por nutricionistas (ergonomía visual)
            $color = match ($estadoCita) {
                'disponible' => '#7BCB87',      // Verde suave (saturación reducida 8% para fatiga visual)
                'confirmada' => '#4A90E2',      // Azul confiable - estado seguro
                'agendada' => '#4A90E2',        // Azul (mismo que confirmada)
                'pendiente' => '#FFB74D',        // Naranjo claro - esperando confirmación
                'en_proceso' => '#FF9800',       // Naranjo intenso - consulta en curso
                'completada' => '#90A4AE',      // Gris azulado - estado finalizado
                'cancelada' => '#E57373',        // Rojo suave - estado negativo (no agresivo)
                'no_asistio' => '#BA68C8',       // Morado suave - estado especial
                'bloqueado' => '#BDBDBD',        // Gris claro - estado inactivo
                'no_disponible' => '#BDBDBD',    // Gris claro - estado inactivo
                default => '#BDBDBD'              // Gris por defecto
            };
            
            // Iconos para modalidad (MODALIDAD = iconos/badges, no colores)
            $iconoModalidad = match ($modalidadId) {
                1 => '🏥',  // Presencial
                2 => '💻',  // Online
                3 => '❔',  // No definido
                default => '❔'
            };
            
            // Construir título con icono de modalidad
            $titulo = '';
            if ($estadoCita === 'cancelada') {
                // Para citas canceladas, mostrar como cancelada incluso si no tiene paciente
                $titulo = $iconoModalidad . ' Cancelada' . ($nombrePaciente !== 'Disponible' ? ' - ' . $nombrePaciente : '');
            } elseif ($estaDisponible) {
                $titulo = $iconoModalidad . ' Disponible';
            } else {
                $titulo = $iconoModalidad . ' ' . $nombrePaciente . ($evento->motivo ? ' - ' . substr($evento->motivo, 0, 25) : '');
            }

            // Para citas completadas, agregar estilo especial para diferenciarlas mejor
            $claseCSS = '';
            $borderColor = null;
            if ($estadoCita === 'completada') {
                $claseCSS = 'fc-event-completada-alt'; // Usar la versión con checkmark
                $borderColor = '#607D8B'; // Borde más oscuro para destacar
            }
            
            $data[] = [
                'id' => $evento->id,
                'title' => $titulo,
                'start' => $fechaInicio,
                'end' => $fechaFin,
                'color' => $color,
                'textColor' => '#FFFFFF', // Texto blanco para mejor contraste
                'borderColor' => $borderColor ?? $color, // Borde del mismo color o más oscuro si está completada
                'classNames' => $claseCSS ? [$claseCSS] : [],
                'extendedProps' => [
                    'paciente_id' => $evento->paciente_id,
                    'estado_cita' => $estadoCita,
                    'tipo_consulta' => $evento->tipo_consulta,
                    'motivo' => $evento->motivo,
                    'modalidad_id' => $modalidadId,
                    'modalidad_icono' => $iconoModalidad
                ]
            ];
        }

        // Incluir la hora mínima en la respuesta para configurar slotMinTime
        return $this->response->setJSON([
            'events' => $data,
            'slotMinTime' => $horaMinimaFormateada
        ]);
    }

    /**
     * Obtener información completa de una cita (detalle_agenda)
     */
    public function getDetalleCita()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $detalleAgendaId = $this->request->getGet('id');
        
        if (!$detalleAgendaId) {
            return $this->response->setJSON(['error' => 'ID requerido'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Obtener información completa de la cita
        $cita = $db->table('detalle_agenda da')
            ->select('da.id, da.hora_inicio, da.hora_fin, da.modalidad_id, da.tipo_consulta, da.motivo, 
                      da.estado_cita, da.fecha_confirmacion, da.fecha_cancelacion, da.motivo_cancelacion,
                      da.observaciones, da.notas_nutricionista, da.fecha_inicio_real, da.fecha_fin_real, da.duracion_real,
                      da.notas_consulta, da.objetivos, da.plan_alimentacion, da.recomendaciones, da.proxima_cita_recomendada,
                      da.fecha, da.paciente_id, da.estado,
                      a.fecha as fecha_agenda,
                      p.nombre, p.apellido, p.telefono, p.email, p.rut_dni,
                      ma.nombre as modalidad_nombre,
                      u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
            ->join('usuario u', 'u.id = da.usuario_id', 'left')
            ->where('da.id', $detalleAgendaId)
            ->where('da.usuario_id', $usuario_id)
            ->get()
            ->getRow();

        if (!$cita) {
            return $this->response->setJSON(['error' => 'Cita no encontrada o sin permisos'])->setStatusCode(404);
        }

        // Formatear datos para la respuesta
        $fecha = $cita->fecha ?: $cita->fecha_agenda;
        $fechaFormateada = $fecha; // Ya está en DD-MM-YYYY
        
        $data = [
            'id' => $cita->id,
            'fecha' => $fechaFormateada,
            'hora_inicio' => date('H:i', strtotime($cita->hora_inicio)),
            'hora_fin' => date('H:i', strtotime($cita->hora_fin)),
            'modalidad' => [
                'id' => $cita->modalidad_id,
                'nombre' => $cita->modalidad_nombre ?? 'No definida'
            ],
            'estado' => $cita->estado == 1 ? 'disponible' : 'ocupado',
            'estado_cita' => isset($cita->estado_cita) && $cita->estado_cita !== '' && $cita->estado_cita !== null ? trim($cita->estado_cita) : ($cita->paciente_id ? 'pendiente' : null),
            'tipo_consulta' => $cita->tipo_consulta ?? null,
            'motivo' => $cita->motivo ?? null,
            'observaciones' => $cita->observaciones ?? null,
            'notas_nutricionista' => $cita->notas_nutricionista ?? null,
            'notas_consulta' => $cita->notas_consulta ?? null,
            'objetivos' => $cita->objetivos ?? null,
            'plan_alimentacion' => $cita->plan_alimentacion ?? null,
            'recomendaciones' => $cita->recomendaciones ?? null,
            'fecha_inicio_real' => $cita->fecha_inicio_real ? date('d-m-Y H:i', strtotime($cita->fecha_inicio_real)) : null,
            'fecha_fin_real' => $cita->fecha_fin_real ? date('d-m-Y H:i', strtotime($cita->fecha_fin_real)) : null,
            'duracion_real' => $cita->duracion_real ?? null,
            'fecha_confirmacion' => $cita->fecha_confirmacion ? date('d-m-Y H:i', strtotime($cita->fecha_confirmacion)) : null,
            'fecha_cancelacion' => $cita->fecha_cancelacion ? date('d-m-Y H:i', strtotime($cita->fecha_cancelacion)) : null,
            'motivo_cancelacion' => $cita->motivo_cancelacion ?? null,
            'paciente' => null,
            'nutricionista' => [
                'nombre' => trim(($cita->nutricionista_nombre ?? '') . ' ' . ($cita->nutricionista_apellido ?? ''))
            ]
        ];

        // Si tiene paciente, agregar información del paciente
        if ($cita->paciente_id) {
            $data['paciente'] = [
                'id' => $cita->paciente_id,
                'nombre' => trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? '')),
                'telefono' => $cita->telefono ?? null,
                'email' => $cita->email ?? null,
                'rut_dni' => $cita->rut_dni ?? null
            ];
        }

        return $this->response->setJSON($data);
    }

    /**
     * Actualizar notas del nutricionista para una cita
     */
    public function actualizarNotasNutricionista()
    {
        // Forzar respuesta JSON desde el inicio
        $this->response->setContentType('application/json');
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $detalleAgendaId = $this->request->getPost('detalle_agenda_id');
        $notas = $this->request->getPost('notas_nutricionista');

        if (!$detalleAgendaId) {
            return $this->response->setJSON([
                'error' => 'Error de validación',
                'message' => 'ID de detalle agenda es requerido'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Verificar que el detalle_agenda pertenece al usuario
        $detalle = $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->where('usuario_id', $usuario_id)
            ->get()
            ->getRow();

        if (!$detalle) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'message' => 'No tiene permiso para modificar este horario'
            ])->setStatusCode(403);
        }

        // Actualizar notas del nutricionista
        $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->update(['notas_nutricionista' => $notas ?: null]);

        // Incluir el nuevo token CSRF en la respuesta
        $response = $this->response->setJSON([
            'success' => true,
            'message' => 'Notas guardadas correctamente',
            'csrf_token' => csrf_hash()
        ]);
        
        $response->setHeader('X-CSRF-TOKEN', csrf_hash());
        
        return $response;
    }

    public function agendar()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado', 'message' => 'Su sesión ha expirado. Por favor, recargue la página.'])->setStatusCode(401);
        }

        $validationRules = [
            'detalle_agenda_id' => 'required|integer|greater_than[0]',
            'paciente_id' => 'required|integer|greater_than[0]'
        ];

        $validationMessages = [
            'detalle_agenda_id' => [
                'required' => 'El horario es obligatorio.',
                'integer' => 'Debe seleccionar un horario válido.',
                'greater_than' => 'Debe seleccionar un horario válido.'
            ],
            'paciente_id' => [
                'required' => 'El paciente es obligatorio.',
                'integer' => 'Debe seleccionar un paciente válido.',
                'greater_than' => 'Debe seleccionar un paciente válido.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            $errors = $this->validator->getErrors();
            $errorMessage = !empty($errors) ? implode(', ', array_values($errors)) : 'Datos inválidos';
            return $this->response->setJSON(['error' => 'Error de validación', 'message' => $errorMessage, 'errors' => $errors])->setStatusCode(400);
        }

        $post = $this->request->getPost([
            'detalle_agenda_id', 'paciente_id', 'tipo_consulta', 'motivo', 'observaciones', 'boton_pago_plantilla_id'
        ]);

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];
        $detalleAgendaId = (int) $post['detalle_agenda_id'];
        $pacienteId = (int) $post['paciente_id'];

        try {
            // Verificar que el detalle_agenda existe y pertenece al usuario
            $detalle = $db->table('detalle_agenda')
                ->where('id', $detalleAgendaId)
                ->where('usuario_id', $usuario_id)
                ->get()
                ->getRow();

            if (!$detalle) {
                return $this->response->setJSON([
                    'error' => 'No autorizado',
                    'message' => 'No tiene permiso para modificar este horario'
                ])->setStatusCode(403);
            }

            // Verificar que el horario esté disponible (no tenga paciente asignado)
            if (!empty($detalle->paciente_id)) {
                return $this->response->setJSON([
                    'error' => 'Horario ocupado',
                    'message' => 'Este horario ya está ocupado por otro paciente'
                ])->setStatusCode(400);
            }

            // Actualizar detalle_agenda con los datos del paciente
            // Si se seleccionó un botón de pago, el estado es 'en_proceso'
            // Si no hay botón de pago, el estado es 'pendiente' (esperando confirmación)
            $botonPagoPlantillaId = $post['boton_pago_plantilla_id'] ?? null;
            $dataUpdate = [
                'paciente_id' => $pacienteId,
                'tipo_consulta' => $post['tipo_consulta'] ?? 'control',
                'motivo' => $post['motivo'] ?? null,
                'observaciones' => $post['observaciones'] ?? null,
                'estado' => 2 // Ocupado
            ];
            
            // Determinar estado según si hay botón de pago
            if (!empty($botonPagoPlantillaId)) {
                // Si hay botón de pago, estado inicial es 'en_proceso'
                $dataUpdate['estado_cita'] = 'en_proceso';
                log_message('info', 'Cita agendada con botón de pago - Estado: en_proceso');
            } else {
                // Si no hay botón de pago, estado inicial es 'pendiente'
                $dataUpdate['estado_cita'] = 'pendiente';
                log_message('info', 'Cita agendada sin botón de pago - Estado: pendiente');
            }

            // Actualizar detalle_agenda
            $updated = $db->table('detalle_agenda')
                ->where('id', $detalleAgendaId)
                ->update($dataUpdate);
            
            // Verificar que el estado se guardó correctamente
            if ($updated) {
                $verificacion = $db->table('detalle_agenda')
                    ->select('estado_cita')
                    ->where('id', $detalleAgendaId)
                    ->get()
                    ->getRow();
                
                // Si el estado no se guardó o quedó NULL, intentar con UPDATE directo
                if ($verificacion && ($verificacion->estado_cita === null || $verificacion->estado_cita === '')) {
                    log_message('warning', 'Estado_cita quedó NULL. Intentando actualizar directamente...');
                    // Intentar primero con 'pendiente', si falla usar 'agendada'
                    $db->query("UPDATE detalle_agenda SET estado_cita = 'pendiente' WHERE id = ?", [$detalleAgendaId]);
                    // Si aún falla, usar 'agendada' como fallback
                    $verificacion2 = $db->table('detalle_agenda')
                        ->select('estado_cita')
                        ->where('id', $detalleAgendaId)
                        ->get()
                        ->getRow();
                    if ($verificacion2 && ($verificacion2->estado_cita === null || $verificacion2->estado_cita === '')) {
                        $db->query("UPDATE detalle_agenda SET estado_cita = 'agendada' WHERE id = ?", [$detalleAgendaId]);
                    }
                }
            }

            if ($updated) {
                // Si se seleccionó una plantilla de pago, crear el pago (PERO NO ENVIAR EMAIL AÚN)
                // El email del botón de pago se enviará cuando el paciente confirme desde el correo
                $botonPagoPlantillaId = $post['boton_pago_plantilla_id'] ?? null;
                if (!empty($botonPagoPlantillaId)) {
                    try {
                        // Solo crear el pago, NO enviar email todavía
                        $this->crearPagoSinEnviarEmail($detalleAgendaId, $pacienteId, $botonPagoPlantillaId);
                    } catch (\Exception $e) {
                        // No fallar el agendamiento si el pago falla, solo loguear
                        log_message('error', 'Error al crear pago: ' . $e->getMessage());
                    }
                }
                
                // Verificar configuración del usuario para enviar email de confirmación
                $configuracionModel = new \App\Models\EmpresaConfiguracion();
                $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($usuario_id);
                
                // Enviar SOLO el email de confirmación al paciente (con botones de confirmar/cancelar)
                // El email del botón de pago se enviará cuando el paciente confirme
                if ($configuracion['enviar_email'] ?? 1) {
                    try {
                        $this->enviarEmailConfirmacion($detalleAgendaId, $pacienteId);
                    } catch (\Exception $e) {
                        // No fallar el agendamiento si el email falla, solo loguear
                        log_message('error', 'Error al enviar email de confirmación: ' . $e->getMessage());
                    }
                } else {
                    log_message('info', 'Email deshabilitado en configuraciones del usuario ID: ' . $usuario_id);
                }

                // El evento del calendario se crea cuando el paciente confirma desde el email
                // (similar a cómo se envía el WhatsApp)

                $response = $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Cita agendada con éxito',
                    'csrf_token' => csrf_hash()
                ]);
                $response->setHeader('X-CSRF-TOKEN', csrf_hash());
                return $response;
            } else {
                return $this->response->setJSON([
                    'error' => 'Error al agendar la cita',
                    'message' => 'No se pudo actualizar el horario. Por favor, intente nuevamente.'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al agendar cita: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'error' => 'Error al agendar la cita',
                'message' => 'Ocurrió un error inesperado. Por favor, intente nuevamente.'
            ])->setStatusCode(500);
        }
    }

    public function confirmarCita()
    {
        error_log('========================================');
        error_log('CONFIRMAR CITA - MÉTODO EJECUTADO (error_log)');
        error_log('Timestamp: ' . date('Y-m-d H:i:s'));
        error_log('URL: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'N/A'));
        error_log('========================================');
        
        log_message('error', '========================================');
        log_message('error', 'CONFIRMAR CITA - MÉTODO EJECUTADO');
        log_message('error', 'Timestamp: ' . date('Y-m-d H:i:s'));
        log_message('error', 'URL: ' . current_url());
        log_message('error', '========================================');
        
        if (!session()->get('usuario')) {
            log_message('warning', 'CONFIRMAR CITA: No hay sesión de usuario');
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $id = $this->request->getPost('id');
        log_message('info', 'CONFIRMAR CITA: ID recibido=' . ($id ?? 'N/A'));
        
        if (!$id) {
            return $this->response->setJSON(['error' => 'ID requerido'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Verificar que el detalle_agenda existe y pertenece al usuario
        $detalle = $db->table('detalle_agenda')
            ->where('id', $id)
            ->where('usuario_id', $usuario_id)
            ->where('paciente_id IS NOT NULL') // Debe tener paciente asignado
            ->get()
            ->getRow();

        if (!$detalle) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'message' => 'No tiene permiso para modificar esta cita o la cita no existe'
            ])->setStatusCode(403);
        }

        // Verificar que la cita esté en estado pendiente, en_proceso o agendada
        $estadosValidos = ['pendiente', 'en_proceso', 'agendada'];
        if (!in_array($detalle->estado_cita, $estadosValidos)) {
            return $this->response->setJSON([
                'error' => 'Estado inválido',
                'message' => 'Solo se pueden confirmar citas que estén en estado pendiente, en_proceso o agendada. Estado actual: ' . ($detalle->estado_cita ?? 'desconocido')
            ])->setStatusCode(400);
        }
        
        // Si está en 'en_proceso', cambiar a 'pendiente' (esperando pago)
        // Si está en 'pendiente' o 'agendada', cambiar a 'confirmada'
        $nuevoEstado = ($detalle->estado_cita === 'en_proceso') ? 'pendiente' : 'confirmada';

        // Actualizar estado según el estado actual
        $updated = $db->table('detalle_agenda')
            ->where('id', $id)
            ->update([
                'estado_cita' => $nuevoEstado,
                'fecha_confirmacion' => date('Y-m-d H:i:s')
            ]);

        if ($updated) {
            // Verificar si hay un pago pendiente asociado a esta cita
            $pagoModel = new \App\Models\Pago();
            $pago = $pagoModel->where('detalle_agenda_id', $id)
                ->where('estado_pago', 'pendiente')
                ->first();
            $tienePago = ($pago && !empty($pago->mp_preference_id));

            // Obtener configuraciones del nutricionista
            $configuracionModel = new \App\Models\EmpresaConfiguracion();
            $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($usuario_id);

            $meetLink = null; // Variable para almacenar el enlace de Meet si se crea

            // Crear evento en el calendario del nutricionista cuando se confirma manualmente
            // IMPORTANTE: Solo crear el evento si NO hay botón de pago
            // Si hay botón de pago, el evento se creará cuando el pago se apruebe
            if (!$tienePago && ($configuracion['crear_evento_calendario'] ?? 1)) {
                try {
                    log_message('info', 'Creando evento en calendario al confirmar cita manualmente (sin botón de pago)');
                    $resultadoCalendario = $this->crearEventoCalendario($id, $usuario_id);
                    if ($resultadoCalendario && isset($resultadoCalendario['meet_link'])) {
                        $meetLink = $resultadoCalendario['meet_link'];
                        log_message('info', 'Enlace de Google Meet obtenido: ' . $meetLink);
                    }
                } catch (\Exception $e) {
                    // No fallar la confirmación si el calendario falla, solo loguear
                    log_message('error', 'Error al crear evento en calendario desde confirmación manual: ' . $e->getMessage());
                }
            } else {
                if ($tienePago) {
                    log_message('info', 'Evento en calendario NO creado al confirmar manualmente (hay botón de pago - se creará cuando se apruebe el pago)');
                } elseif (!($configuracion['crear_evento_calendario'] ?? 1)) {
                    log_message('info', 'Creación de evento en calendario deshabilitada en configuraciones del usuario ID: ' . $usuario_id);
                }
            }

            // No reenviar el mismo correo de confirmación: ya se envió al agendar. Solo WhatsApp si está habilitado.
            // Enviar WhatsApp cuando se confirma la cita (solo si está habilitado)
            // Incluir el enlace de Meet si está disponible (para citas online)
            log_message('info', 'CONFIRMAR CITA (Dashboard): Verificando configuración de WhatsApp');
            log_message('info', 'CONFIRMAR CITA (Dashboard): enviar_whatsapp=' . ($configuracion['enviar_whatsapp'] ?? 'N/A'));
            
            if ($configuracion['enviar_whatsapp'] ?? 1) {
                log_message('info', 'CONFIRMAR CITA (Dashboard): WhatsApp habilitado, procediendo a enviar');
                log_message('info', 'CONFIRMAR CITA (Dashboard): id=' . $id . ', paciente_id=' . ($detalle->paciente_id ?? 'N/A') . ', meetLink=' . ($meetLink ? 'SÍ' : 'NO'));
                try {
                    $this->enviarWhatsAppConfirmacion($id, $detalle->paciente_id, $meetLink);
                } catch (\Exception $e) {
                    log_message('error', 'CONFIRMAR CITA (Dashboard): Excepción al enviar WhatsApp: ' . $e->getMessage());
                    log_message('error', 'CONFIRMAR CITA (Dashboard): Stack trace: ' . $e->getTraceAsString());
                }
            } else {
                log_message('info', 'CONFIRMAR CITA (Dashboard): WhatsApp deshabilitado en configuraciones del usuario ID: ' . $usuario_id);
            }

            $response = $this->response->setJSON([
                'success' => true, 
                'message' => 'Cita confirmada',
                'csrf_token' => csrf_hash()
            ]);
            $response->setHeader('X-CSRF-TOKEN', csrf_hash());
            return $response;
        } else {
            return $this->response->setJSON(['error' => 'Error al confirmar la cita'])->setStatusCode(500);
        }
    }

    public function cancelarCita()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $id = $this->request->getPost('id');
        $motivo = $this->request->getPost('motivo');
        
        if (!$id) {
            return $this->response->setJSON(['error' => 'ID requerido'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Obtener datos completos de la cita ANTES de cancelar (para enviar email al paciente, igual que módulo Cancelar Horas)
        $citaCompleta = $db->table('detalle_agenda da')
            ->select('da.id, da.fecha, da.hora_inicio, da.hora_fin, da.estado_cita, da.paciente_id,
                     p.nombre as paciente_nombre, p.apellido as paciente_apellido, p.email as paciente_email,
                     u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('usuario u', 'u.id = da.usuario_id', 'left')
            ->where('da.id', $id)
            ->where('da.usuario_id', $usuario_id)
            ->where('da.paciente_id IS NOT NULL')
            ->get()
            ->getRow();

        if (!$citaCompleta) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'message' => 'No tiene permiso para modificar esta cita o la cita no existe'
            ])->setStatusCode(403);
        }

        // Actualizar estado de la cita en detalle_agenda
        // Al cancelar, liberamos el horario (paciente_id = NULL, estado = 1)
        $dataUpdate = [
            'estado_cita' => 'cancelada',
            'fecha_cancelacion' => date('Y-m-d H:i:s'),
            'paciente_id' => null, // Liberar el horario
            'estado' => 1, // Disponible nuevamente
            'motivo_cancelacion' => $motivo ?? null
        ];

        $updated = $db->table('detalle_agenda')
            ->where('id', $id)
            ->update($dataUpdate);

        if ($updated) {
            // Enviar email al paciente (mismo correo que módulo Cancelar Horas)
            if (!empty($citaCompleta->paciente_email)) {
                $configuracionModel = new EmpresaConfiguracion();
                $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($usuario_id);
                if ($configuracion['enviar_email'] ?? 1) {
                    try {
                        $this->enviarEmailCancelacionPaciente($citaCompleta, $motivo);
                    } catch (\Exception $e) {
                        log_message('error', 'CANCELAR CITA (Lista): Error al enviar email al paciente: ' . $e->getMessage());
                    }
                }
            }

            $response = $this->response->setJSON([
                'success' => true, 
                'message' => 'Cita cancelada y horario liberado',
                'csrf_token' => csrf_hash()
            ]);
            $response->setHeader('X-CSRF-TOKEN', csrf_hash());
            return $response;
        } else {
            return $this->response->setJSON(['error' => 'Error al cancelar la cita'])->setStatusCode(500);
        }
    }

    /**
     * Enviar email de cancelación al paciente (misma plantilla y lógica que módulo Cancelar Horas)
     */
    private function enviarEmailCancelacionPaciente($cita, $motivoCancelacion = null)
    {
        if (empty($cita->paciente_email)) {
            return false;
        }

        $configuracionModel = new EmpresaConfiguracion();
        $usuario_id = session()->get('usuario')['id'];
        $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($usuario_id);

        $estadoCita = $cita->estado_cita ?? 'pendiente';
        $mapEstados = [
            'pendiente' => 'mensaje_cancelacion_pendiente',
            'confirmada' => 'mensaje_cancelacion_confirmada',
            'agendada' => 'mensaje_cancelacion_pendiente',
            'en_proceso' => 'mensaje_cancelacion_en_proceso'
        ];
        $campo = $mapEstados[$estadoCita] ?? 'mensaje_cancelacion_pendiente';
        $mensaje = $configuracion[$campo] ?? '';

        if (empty($mensaje)) {
            $mensaje = "Estimado/a [NOMBRE_PACIENTE],\n\nLamentamos informarle que su cita programada para el [FECHA] a las [HORA] ha sido cancelada.\n\nPor favor, contáctenos para reagendar su consulta.\n\nSaludos,\n[NOMBRE_NUTRICIONISTA]";
        }

        if ($motivoCancelacion) {
            $mensaje .= "\n\nMotivo: " . htmlspecialchars(trim($motivoCancelacion), ENT_QUOTES, 'UTF-8');
        }

        $fechaRaw = $cita->fecha ?? '';
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fechaRaw, $m)) {
            $fechaFormateada = $m[1] . '/' . $m[2] . '/' . $m[3];
        } else {
            $fechaFormateada = $fechaRaw ? date('d/m/Y', strtotime(str_replace('/', '-', $fechaRaw))) : '';
        }
        $horaFormateada = date('H:i', strtotime($cita->hora_inicio));
        $nombrePaciente = trim(($cita->paciente_nombre ?? '') . ' ' . ($cita->paciente_apellido ?? ''));
        $nombreNutricionista = trim(($cita->nutricionista_nombre ?? '') . ' ' . ($cita->nutricionista_apellido ?? ''));

        $mensaje = str_replace('[NOMBRE_PACIENTE]', $nombrePaciente, $mensaje);
        $mensaje = str_replace('[FECHA]', $fechaFormateada, $mensaje);
        $mensaje = str_replace('[HORA]', $horaFormateada, $mensaje);
        $mensaje = str_replace('[NOMBRE_NUTRICIONISTA]', $nombreNutricionista, $mensaje);

        // Si el mensaje viene de BD/config con "\n" literal (backslash+n), convertirlo a salto real para que nl2br genere <br>
        $mensaje = str_replace(["\\n", "\\r\\n", "\\r"], ["\n", "\n", "\n"], $mensaje);
        $mensajeHtml = nl2br($mensaje);

        $email = Services::email();
        $email->setFrom(env('email.fromEmail', 'noreply@example.com'), env('email.fromName', 'Sistema de Agenda'));
        $email->setTo($cita->paciente_email);
        $email->setSubject('Cancelación de Cita - ' . ($nombreNutricionista ?: 'Nutricionista'));
        $email->setMessage(view('emails/cancelacion_cita_paciente', [
            'paciente_nombre' => $nombrePaciente,
            'fecha' => $fechaFormateada,
            'hora' => $horaFormateada,
            'mensaje' => $mensajeHtml,
            'nutricionista_nombre' => $nombreNutricionista
        ]));
        return $email->send();
    }

    public function getAgenda()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $draw = intval($this->request->getGet("draw"));
        
        // Asegurar que solo se muestren las citas del usuario logueado (nutricionista)
        $usuario_id = session()->get('usuario')['id'];
        
        $fecha_desde = $this->request->getGet('fecha_desde');
        $fecha_hasta = $this->request->getGet('fecha_hasta');
        $estado_cita = $this->request->getGet('estado_cita');
        
        // Obtener citas desde detalle_agenda (donde paciente_id no es NULL)
        $builder = $db->table('detalle_agenda da')
            ->select('da.id, da.paciente_id, da.tipo_consulta, da.motivo, da.estado_cita, da.fecha_confirmacion, da.fecha_cancelacion, da.motivo_cancelacion, da.observaciones, a.fecha, da.hora_inicio, da.hora_fin, p.nombre, p.apellido, p.telefono, p.email')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->where('da.paciente_id IS NOT NULL') // Solo citas agendadas
            ->where('da.usuario_id', $usuario_id); // Solo mostrar citas del usuario logueado

        if ($fecha_desde) {
            // Convertir fecha para comparación (fecha en BD está en DD-MM-YYYY)
            $builder->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') >= ", $fecha_desde);
        }
        if ($fecha_hasta) {
            // Convertir fecha para comparación (fecha en BD está en DD-MM-YYYY)
            $builder->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') <= ", $fecha_hasta);
        }
        if ($estado_cita) {
            $builder->where('da.estado_cita', $estado_cita);
        }

        $rows = $builder->orderBy('a.fecha', 'DESC')
                       ->orderBy('da.hora_inicio', 'ASC')
                       ->get()->getResult();

        $data = array();
        foreach ($rows as $r) {
            $nombrePaciente = trim(($r->nombre ?? '') . ' ' . ($r->apellido ?? ''));

            $estadoBadge = match (strtolower(trim((string)($r->estado_cita ?? '')))) {
                'pendiente' => '<span class="badge bg-warning text-dark">Pendiente</span>',
                'agendada' => '<span class="badge bg-primary">Agendada</span>',
                'confirmada' => '<span class="badge bg-success">Confirmada</span>',
                'en_proceso' => '<span class="badge bg-warning">En Proceso</span>',
                'completada' => '<span class="badge bg-info">Completada</span>',
                'cancelada' => '<span class="badge bg-danger">Cancelada</span>',
                'no_asistio' => '<span class="badge bg-secondary">No Asistió</span>',
                default => '<span class="badge bg-secondary">N/A</span>',
            };

            // Usar detalle_agenda_id en lugar de agenda_paciente.id
            $botones = '<button class="btn btn-sm btn-outline-success" onclick="confirmarCita(' . $r->id . ')">Confirmar</button> ' .
                      '<button class="btn btn-sm btn-outline-danger" onclick="cancelarCita(' . $r->id . ')">Cancelar</button> ' .
                      '<button class="btn btn-sm btn-outline-info" onclick="verCita(' . $r->id . ')">Ver</button>';

            $data[] = array(
                esc($nombrePaciente),
                esc($r->fecha ?: ''), // La fecha ya está en formato DD-MM-YYYY en la BD
                esc($r->hora_inicio ?? ''),
                esc($r->hora_fin ?? ''),
                $estadoBadge,
                esc($r->motivo ?? ''),
                $botones
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function getAgendas()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $draw = intval($this->request->getGet("draw"));
        $usuario_id = session()->get('usuario')['id'];
        
        // Obtener todas las AGENDAS (días creados) del usuario
        // Una agenda es un día completo con su horario laboral
        // Solo mostrar agendas que tienen al menos un detalle_agenda del usuario
        $builder = $db->table('agenda a')
            ->select('a.id, a.fecha, a.hora_inicio, a.hora_fin, a.almuerzo_inicio, a.almuerzo_fin,
                      COUNT(DISTINCT CASE WHEN da.estado = 1 AND da.paciente_id IS NULL THEN da.id END) as disponibles,
                      COUNT(DISTINCT CASE WHEN da.paciente_id IS NOT NULL THEN da.id END) as ocupados,
                      COUNT(DISTINCT da.id) as total_horarios')
            ->join('detalle_agenda da', 'da.agenda_id = a.id AND da.usuario_id = ' . $usuario_id, 'inner')
            ->groupBy('a.id, a.fecha, a.hora_inicio, a.hora_fin, a.almuerzo_inicio, a.almuerzo_fin')
            ->orderBy('a.fecha', 'DESC');

        $rows = $builder->get()->getResult();

        $data = array();
        foreach ($rows as $r) {
            // Formatear almuerzo
            $almuerzo = '';
            if ($r->almuerzo_inicio && $r->almuerzo_fin) {
                $almuerzo = date('H:i', strtotime($r->almuerzo_inicio)) . ' - ' . date('H:i', strtotime($r->almuerzo_fin));
            } else {
                $almuerzo = '<span class="text-muted">Sin almuerzo</span>';
            }

            // Botón Ver que lleva al calendario
            $botones = '<a href="' . base_url('dashboard/agenda/calendario') . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i> Ver Calendario
                        </a>';

            // Formatear fecha a día-mes-año (DD-MM-YYYY)
            // La fecha ya está en formato DD-MM-YYYY en la BD
            $data[] = array(
                esc($r->fecha ?: ''), // Fecha del día en formato DD-MM-YYYY
                date('H:i', strtotime($r->hora_inicio)), // Hora inicio del día laboral
                date('H:i', strtotime($r->hora_fin)), // Hora fin del día laboral
                $almuerzo, // Horario de almuerzo
                '<span class="badge bg-success">' . ($r->disponibles ?? 0) . '</span>', // Horarios disponibles
                '<span class="badge bg-warning">' . ($r->ocupados ?? 0) . '</span>', // Horarios ocupados
                $botones // Acciones
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function actualizarModalidad()
    {
        // Forzar respuesta JSON desde el inicio para evitar redirecciones
        $this->response->setContentType('application/json');
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $detalleAgendaId = $this->request->getPost('detalle_agenda_id');
        $modalidadId = $this->request->getPost('modalidad_id');

        if (!$detalleAgendaId || !$modalidadId) {
            return $this->response->setJSON([
                'error' => 'Error de validación',
                'message' => 'ID de detalle agenda y modalidad son requeridos'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Verificar que el detalle_agenda pertenece al usuario
        $detalle = $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->where('usuario_id', $usuario_id)
            ->get()
            ->getRow();

        if (!$detalle) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'message' => 'No tiene permiso para modificar este horario'
            ])->setStatusCode(403);
        }

        // Actualizar modalidad
        $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->update(['modalidad_id' => $modalidadId]);

        // Incluir el nuevo token CSRF en la respuesta
        $response = $this->response->setJSON([
            'success' => true,
            'message' => 'Modalidad actualizada correctamente',
            'csrf_token' => csrf_hash()
        ]);
        
        // También enviar en el header (CodeIgniter lo hace automáticamente, pero lo hacemos explícito)
        $response->setHeader('X-CSRF-TOKEN', csrf_hash());
        
        return $response;
    }

    public function crearHorarios()
    {
        // Forzar respuesta JSON desde el inicio para evitar redirecciones
        $this->response->setContentType('application/json');
        
        // Verificar si es una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'error' => 'Solicitud inválida',
                'message' => 'Esta acción solo está disponible mediante AJAX.'
            ])->setStatusCode(400);
        }
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'message' => 'Su sesión ha expirado. Por favor, recargue la página.'
            ])->setStatusCode(401);
        }

        try {
            // Validar manualmente primero para evitar redirecciones
            $dias = $this->request->getPost('dias');
            $duracion = $this->request->getPost('duracion');
            $horaInicio = $this->request->getPost('hora_inicio');
            $horaFin = $this->request->getPost('hora_fin');
            $diasSemana = $this->request->getPost('dias_semana');
            $modalidadId = $this->request->getPost('modalidad_id') ?: 3; // Por defecto "No Definido"
            
            $errors = [];
            
            // Validar días
            if (empty($dias) || !is_numeric($dias) || (int)$dias <= 0 || (int)$dias > 365) {
                $errors['dias'] = 'El número de días debe ser un número entre 1 y 365.';
            }
            
            // Validar duración
            if (empty($duracion) || !is_numeric($duracion) || (int)$duracion < 5 || (int)$duracion > 480) {
                $errors['duracion'] = 'La duración debe ser un número entre 5 y 480 minutos.';
            }
            
            // Validar hora inicio
            if (empty($horaInicio) || !preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $horaInicio)) {
                $errors['hora_inicio'] = 'La hora de inicio debe tener un formato válido (HH:MM).';
            }
            
            // Validar hora fin
            if (empty($horaFin) || !preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $horaFin)) {
                $errors['hora_fin'] = 'La hora de fin debe tener un formato válido (HH:MM).';
            }
            
            // Validar días de la semana
            if (empty($diasSemana) || !is_array($diasSemana) || count($diasSemana) === 0) {
                $errors['dias_semana'] = 'Debe seleccionar al menos un día de la semana.';
            }
            
            // Si hay errores, devolver JSON
            if (!empty($errors)) {
                return $this->response->setJSON([
                    'error' => 'Error de validación',
                    'message' => implode(', ', array_values($errors)),
                    'errors' => $errors
                ])->setStatusCode(400);
            }

            // Convertir a tipos correctos
            $dias = (int) $dias;
            $duracion = (int) $duracion;
            $diasSemana = is_array($diasSemana) ? $diasSemana : [];
            $incluirAlmuerzo = $this->request->getPost('incluir_almuerzo') == 'true' || $this->request->getPost('incluir_almuerzo') == 'on';
            $almuerzoInicio = $this->request->getPost('almuerzo_inicio');
            $almuerzoFin = $this->request->getPost('almuerzo_fin');
            
            $usuario_id = session()->get('usuario')['id'];
            
            $db = \Config\Database::connect();
            $horariosCreados = 0;
            
            // Validar que hora fin sea mayor que hora inicio
            if (strtotime($horaFin . ':00') <= strtotime($horaInicio . ':00')) {
                return $this->response->setJSON([
                    'error' => 'Error de validación',
                    'message' => 'La hora de fin debe ser mayor que la hora de inicio'
                ])->setStatusCode(400);
            }
            
            // Obtener fecha de inicio (si no se proporciona, usar hoy)
            $fechaInicioInput = $this->request->getPost('fecha_inicio');
            $fechaInicio = $fechaInicioInput ? $fechaInicioInput : date('Y-m-d');
            
            // Validar que la fecha de inicio no sea anterior a hoy
            if ($fechaInicio < date('Y-m-d')) {
                return $this->response->setJSON([
                    'error' => 'Error de validación',
                    'message' => 'La fecha de inicio no puede ser anterior a hoy'
                ])->setStatusCode(400);
            }
            
            // Convertir días de la semana a enteros
            $diasSemanaInt = array_map('intval', $diasSemana);
            
            // Crear horarios contando solo los días seleccionados
            // Si el usuario quiere 10 días y seleccionó lunes a viernes,
            // debemos crear horarios para 10 días laborables, no 10 días calendario
            $fechaActual = new \DateTime($fechaInicio);
            $diasCreados = 0;
            $maxIteraciones = 365; // Límite de seguridad para evitar loops infinitos
            
            while ($diasCreados < $dias && $maxIteraciones > 0) {
                $diaSemana = (int) $fechaActual->format('w'); // 0=Domingo, 6=Sábado
                
                // Verificar si este día está en los días seleccionados
                if (in_array($diaSemana, $diasSemanaInt)) {
                    $fecha = $fechaActual->format('d-m-Y'); // Guardar en formato día-mes-año
                    
                    // Verificar si ya existe agenda para esta fecha
                    $agenda = $db->table('agenda')
                        ->where('fecha', $fecha)
                        ->get()
                        ->getRow();
                    
                    if (!$agenda) {
                        // Obtener o crear tipo_agenda básico (si es necesario)
                        // Para nutricionistas, tipo_agenda es opcional, pero la BD lo requiere
                        $tipoAgenda = $db->table('tipo_agenda')
                            ->where('id', 1)
                            ->get()
                            ->getRow();
                        
                        $tipoId = null;
                        if ($tipoAgenda) {
                            $tipoId = 1;
                        } else {
                            // Intentar crear el tipo básico
                            try {
                                $db->table('tipo_agenda')->insert([
                                    'id' => 1,
                                    'nombre' => 'Consulta Normal'
                                ]);
                                $tipoId = 1;
                            } catch (\Exception $e) {
                                // Si no se puede crear, intentar obtener cualquier tipo disponible
                                $tipoDisponible = $db->table('tipo_agenda')
                                    ->limit(1)
                                    ->get()
                                    ->getRow();
                                if ($tipoDisponible) {
                                    $tipoId = $tipoDisponible->id;
                                } else {
                                    // Si no hay tipos, usar NULL (requiere que la columna permita NULL)
                                    // O lanzar error si es NOT NULL
                                    log_message('warning', 'No se encontró tipo_agenda. Usando valor por defecto.');
                                    $tipoId = 1; // Intentar con 1 de todas formas
                                }
                            }
                        }
                        
                        // Crear registro en agenda
                        // NOTA: tipo_id es requerido por la estructura actual, pero modalidad_id
                        // (presencial/online) se define en detalle_agenda para cada hora
                        $db->table('agenda')->insert([
                            'fecha' => $fecha,
                            'hora_inicio' => $horaInicio . ':00',
                            'hora_fin' => $horaFin . ':00',
                            'almuerzo_inicio' => $incluirAlmuerzo ? ($almuerzoInicio . ':00') : null,
                            'almuerzo_fin' => $incluirAlmuerzo ? ($almuerzoFin . ':00') : null,
                            'estado_id' => null,
                            'tipo_id' => $tipoId,
                            'usuario_id' => $usuario_id // Agregar usuario_id (nutricionista)
                        ]);
                        $agendaId = $db->insertID();
                    } else {
                        $agendaId = $agenda->id;
                    }
                    
                    // Crear horarios según la duración especificada
                    $horaInicioObj = new \DateTime($fecha . ' ' . $horaInicio . ':00');
                    $horaFinObj = new \DateTime($fecha . ' ' . $horaFin . ':00');
                    $almuerzoInicioObj = $incluirAlmuerzo ? new \DateTime($fecha . ' ' . $almuerzoInicio . ':00') : null;
                    $almuerzoFinObj = $incluirAlmuerzo ? new \DateTime($fecha . ' ' . $almuerzoFin . ':00') : null;
                    
                    $orden = 1;
                    $horaActual = clone $horaInicioObj;
                    
                    while ($horaActual < $horaFinObj) {
                        $horaFinCita = clone $horaActual;
                        $horaFinCita->modify("+{$duracion} minutes");
                        
                        // Si hay horario de almuerzo, saltarlo
                        if ($incluirAlmuerzo && $almuerzoInicioObj && $almuerzoFinObj) {
                            if ($horaActual >= $almuerzoInicioObj && $horaActual < $almuerzoFinObj) {
                                $horaActual = clone $almuerzoFinObj;
                                continue;
                            }
                            // Si el horario se solapa con el almuerzo, ajustarlo
                            if ($horaActual < $almuerzoFinObj && $horaFinCita > $almuerzoInicioObj) {
                                $horaActual = clone $almuerzoFinObj;
                                continue;
                            }
                        }
                        
                        // Verificar si ya existe este horario
                        $existe = $db->table('detalle_agenda')
                            ->where('agenda_id', $agendaId)
                            ->where('usuario_id', $usuario_id)
                            ->where('hora_inicio', $horaActual->format('H:i:s'))
                            ->get()
                            ->getRow();
                        
                        if (!$existe) {
                            $db->table('detalle_agenda')->insert([
                                'agenda_id' => $agendaId,
                                'fecha' => $fecha, // Agregar fecha
                                'usuario_id' => $usuario_id,
                                'orden' => $orden++,
                                'hora_inicio' => $horaActual->format('H:i:s'),
                                'hora_fin' => $horaFinCita->format('H:i:s'),
                                'estado' => 1, // Disponible
                                'estado_solicitud_id' => 1,
                                'modalidad_id' => $modalidadId, // Usar la modalidad seleccionada
                                'forma_asignacion' => 'Manual',
                                'estado_cita' => NULL // NULL = Disponible (sin paciente asignado)
                            ]);
                            $horariosCreados++;
                        }
                        
                        $horaActual = $horaFinCita;
                    }
                    
                    // Incrementar contador solo si se procesó un día seleccionado
                    $diasCreados++;
                }
                
                // Avanzar al siguiente día
                $fechaActual->modify('+1 day');
                $maxIteraciones--;
            }
            
            $response = $this->response->setJSON([
                'success' => true,
                'message' => "Se crearon {$horariosCreados} horarios disponibles exitosamente",
                'horarios_creados' => $horariosCreados,
                'csrf_token' => csrf_hash()
            ]);
            $response->setHeader('X-CSRF-TOKEN', csrf_hash());
            return $response;
            
        } catch (\Exception $e) {
            log_message('error', 'Error al crear horarios: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'error' => 'Error al crear horarios',
                'message' => 'Ocurrió un error inesperado: ' . $e->getMessage()
            ])->setStatusCode(500);
        } catch (\Throwable $e) {
            log_message('error', 'Error fatal al crear horarios: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Error al crear horarios',
                'message' => 'Ocurrió un error inesperado. Por favor, intente nuevamente.'
            ])->setStatusCode(500);
        }
    }

    public function eliminarHorarios()
    {
        // Forzar respuesta JSON desde el inicio
        $this->response->setContentType('application/json');
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'message' => 'Su sesión ha expirado. Por favor, recargue la página.'
            ])->setStatusCode(401);
        }

        try {
            $usuario_id = session()->get('usuario')['id'];
            $fechaDesde = $this->request->getPost('fecha_desde');
            $fechaHasta = $this->request->getPost('fecha_hasta');
            $soloDisponibles = $this->request->getPost('solo_disponibles') == 'true' || $this->request->getPost('solo_disponibles') == 'on';
            
            $db = \Config\Database::connect();
            
            // Construir query para eliminar horarios disponibles sin citas
            $builder = $db->table('detalle_agenda da')
                ->select('da.id')
                ->join('agenda a', 'a.id = da.agenda_id', 'left')
                ->where('da.usuario_id', $usuario_id);
            
            if ($soloDisponibles) {
                $builder->where('da.estado', 1); // Solo disponibles
            }
            
            if ($fechaDesde) {
                // Convertir fecha para comparación (fecha en BD está en DD-MM-YYYY)
                $builder->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') >= ", $fechaDesde);
            }
            if ($fechaHasta) {
                // Convertir fecha para comparación (fecha en BD está en DD-MM-YYYY)
                $builder->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') <= ", $fechaHasta);
            }
            
            // Solo eliminar los que no tienen citas agendadas (paciente_id es NULL)
            $builder->where('da.paciente_id IS NULL');
            
            // Obtener IDs a eliminar
            $idsAEliminar = $builder->get()->getResultArray();
            $ids = array_column($idsAEliminar, 'id');
            
            if (empty($ids)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'No hay horarios disponibles para eliminar',
                    'horarios_eliminados' => 0
                ]);
            }
            
            // Eliminar los horarios
            $horariosEliminados = $db->table('detalle_agenda')
                ->whereIn('id', $ids)
                ->delete();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Se eliminaron {$horariosEliminados} horarios disponibles exitosamente",
                'horarios_eliminados' => $horariosEliminados
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar horarios: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'error' => 'Error al eliminar horarios',
                'message' => 'Ocurrió un error inesperado: ' . $e->getMessage()
            ])->setStatusCode(500);
        } catch (\Throwable $e) {
            log_message('error', 'Error fatal al eliminar horarios: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Error al eliminar horarios',
                'message' => 'Ocurrió un error inesperado. Por favor, intente nuevamente.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Enviar email de confirmación al paciente
     */
    private function enviarEmailConfirmacion($detalleAgendaId, $pacienteId)
    {
        $db = \Config\Database::connect();
        
        // Obtener información completa de la cita
        // IMPORTANTE: Usar da.usuario_id (nutricionista dueño del horario) en lugar de a.usuario_id
        $cita = $db->table('detalle_agenda da')
            ->select('da.id, da.hora_inicio, da.hora_fin, da.modalidad_id, da.tipo_consulta, da.motivo, da.usuario_id,
                      a.fecha,
                      p.nombre, p.apellido, p.email as paciente_email,
                      u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido, u.correo as nutricionista_email,
                      ma.nombre as modalidad_nombre')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('usuario u', 'u.id = da.usuario_id', 'left') // Cambiar a da.usuario_id para obtener el nutricionista correcto
            ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
            ->where('da.id', $detalleAgendaId)
            ->where('da.paciente_id', $pacienteId)
            ->get()
            ->getRow();

        if (!$cita || empty($cita->paciente_email)) {
            log_message('warning', 'No se puede enviar email: cita no encontrada o paciente sin email');
            return false;
        }

        // Generar token único para confirmar/cancelar desde el email
        $token = bin2hex(random_bytes(32));
        
        // Guardar token en la sesión o en una tabla temporal (por ahora usaremos un hash del ID)
        // Para producción, deberías crear una tabla de tokens con expiración
        $tokenConfirmar = base64_encode($detalleAgendaId . '|' . $pacienteId . '|' . hash('sha256', $detalleAgendaId . $pacienteId . 'confirmar'));
        $tokenCancelar = base64_encode($detalleAgendaId . '|' . $pacienteId . '|' . hash('sha256', $detalleAgendaId . $pacienteId . 'cancelar'));

        // Preparar datos para el email
        $fechaFormateada = $cita->fecha; // Ya está en DD-MM-YYYY
        $horaInicio = date('H:i', strtotime($cita->hora_inicio));
        $horaFin = date('H:i', strtotime($cita->hora_fin));
        $nombrePaciente = trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? ''));
        $nombreNutricionista = trim(($cita->nutricionista_nombre ?? '') . ' ' . ($cita->nutricionista_apellido ?? ''));
        $modalidadNombre = $cita->modalidad_nombre ?? 'No definida';
        $tipoConsulta = ucfirst(str_replace('_', ' ', $cita->tipo_consulta ?? 'control'));

        // Crear el mensaje HTML
        $mensaje = view('emails/confirmacion_cita', [
            'nombrePaciente' => $nombrePaciente,
            'nombreNutricionista' => $nombreNutricionista,
            'fecha' => $fechaFormateada,
            'horaInicio' => $horaInicio,
            'horaFin' => $horaFin,
            'modalidad' => $modalidadNombre,
            'tipoConsulta' => $tipoConsulta,
            'motivo' => $cita->motivo ?? '',
            'tokenConfirmar' => $tokenConfirmar,
            'tokenCancelar' => $tokenCancelar,
            'baseUrl' => base_url()
        ]);

        // Enviar email
        $email = Services::email();
        $email->setFrom(env('email.fromEmail', 'noreply@example.com'), env('email.fromName', 'Sistema de Agenda'));
        $email->setTo($cita->paciente_email);
        $email->setSubject('📅 Confirmación de Cita - ' . $fechaFormateada . ' a las ' . $horaInicio);
        $email->setMessage($mensaje);

        if ($email->send()) {
            log_message('info', 'Email de confirmación enviado a: ' . $cita->paciente_email);
            return true;
        } else {
            log_message('error', 'Error al enviar email: ' . $email->printDebugger(['headers']));
            return false;
        }
    }

    /**
     * Crear pago desde plantilla SIN enviar email (el email se enviará cuando el paciente confirme)
     */
    private function crearPagoSinEnviarEmail($detalleAgendaId, $pacienteId, $plantillaId)
    {
        $db = \Config\Database::connect();
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        
        if (!$empresaId) {
            throw new \Exception('No se pudo obtener la empresa del usuario');
        }

        // Obtener plantilla
        $plantillaModel = new \App\Models\BotonPagoPlantilla();
        $plantilla = $plantillaModel->getPlantilla($plantillaId, $empresaId);
        
        if (!$plantilla) {
            throw new \Exception('Plantilla de pago no encontrada');
        }

        // Obtener datos del paciente
        $pacienteModel = new \App\Models\Paciente();
        $paciente = $pacienteModel->find($pacienteId);
        
        if (!$paciente || empty($paciente->email)) {
            throw new \Exception('Paciente no encontrado o sin email');
        }

        // Obtener datos de la cita
        // IMPORTANTE: La hora está en detalle_agenda (da.hora_inicio), no en agenda (a.hora_inicio)
        $cita = $db->table('detalle_agenda da')
            ->select('da.*, a.fecha, da.hora_inicio, da.hora_fin')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->where('da.id', $detalleAgendaId)
            ->get()
            ->getRow();

        if (!$cita) {
            throw new \Exception('Cita no encontrada');
        }

        // Verificar que Mercado Pago esté configurado
        $empresaConfigModel = new \App\Models\EmpresaConfiguracion();
        if (!$empresaConfigModel->mercadoPagoHabilitado($empresaId)) {
            throw new \Exception('Mercado Pago no está configurado para esta empresa');
        }

        // Crear registro de pago
        $pagoModel = new \App\Models\Pago();
        $pagoData = [
            'empresa_id' => $empresaId,
            'detalle_agenda_id' => $detalleAgendaId,
            'tipo_pago' => 'cita',
            'monto' => $plantilla->monto,
            'moneda' => $plantilla->moneda,
            'estado_pago' => 'pendiente',
            'observaciones' => 'Pago generado automáticamente al agendar cita. Plantilla: ' . $plantilla->titulo
        ];

        $pagoId = $pagoModel->insert($pagoData);
        
        if (!$pagoId) {
            throw new \Exception('Error al crear el registro de pago');
        }

        // Obtener servicio de Mercado Pago
        $credenciales = $empresaConfigModel->obtenerCredencialesMercadoPago($empresaId);
        $webhookBaseUrl = rtrim(base_url(), '/');
        
        $mercadoPagoService = new \App\Services\MercadoPagoService(
            $credenciales['access_token'],
            $credenciales['public_key'],
            $credenciales['mode'],
            $webhookBaseUrl
        );

        // Crear preferencia de pago
        $preferenciaData = [
            'title' => $plantilla->titulo,
            'description' => $plantilla->descripcion ?? 'Pago de consulta nutricional',
            'unit_price' => $plantilla->monto,
            'quantity' => 1,
            'currency' => $plantilla->moneda,
            'payer_email' => $paciente->email,
            'payer_name' => $paciente->nombre ?? '',
            'payer_surname' => $paciente->apellido ?? '',
            'external_reference' => (string)$pagoId,
            'statement_descriptor' => 'NextLine Nutrición'
        ];

        $preferencia = $mercadoPagoService->crearPreferencia($preferenciaData);

        // Actualizar pago con preference_id
        $pagoModel->update($pagoId, [
            'mp_preference_id' => $preferencia['preference_id']
        ]);

        // NO enviar email aquí - se enviará cuando el paciente confirme desde el correo
        log_message('info', 'Pago creado (sin enviar email aún). Pago ID: ' . $pagoId . ', Email: ' . $paciente->email);
    }

    /**
     * Crear pago desde plantilla y enviar botón de pago por email al paciente
     * (Este método se usa cuando el paciente confirma desde el correo)
     */
    private function crearPagoYEnviarBoton($detalleAgendaId, $pacienteId, $plantillaId)
    {
        $db = \Config\Database::connect();
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        
        if (!$empresaId) {
            throw new \Exception('No se pudo obtener la empresa del usuario');
        }

        // Obtener plantilla
        $plantillaModel = new \App\Models\BotonPagoPlantilla();
        $plantilla = $plantillaModel->getPlantilla($plantillaId, $empresaId);
        
        if (!$plantilla) {
            throw new \Exception('Plantilla de pago no encontrada');
        }

        // Obtener datos del paciente
        $pacienteModel = new \App\Models\Paciente();
        $paciente = $pacienteModel->find($pacienteId);
        
        if (!$paciente || empty($paciente->email)) {
            throw new \Exception('Paciente no encontrado o sin email');
        }

        // Obtener datos de la cita
        // IMPORTANTE: La hora está en detalle_agenda (da.hora_inicio), no en agenda (a.hora_inicio)
        $cita = $db->table('detalle_agenda da')
            ->select('da.*, a.fecha, da.hora_inicio, da.hora_fin')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->where('da.id', $detalleAgendaId)
            ->get()
            ->getRow();

        if (!$cita) {
            throw new \Exception('Cita no encontrada');
        }

        // Verificar si ya existe un pago para esta cita
        $pagoModel = new \App\Models\Pago();
        $pagoExistente = $pagoModel->where('detalle_agenda_id', $detalleAgendaId)
            ->where('estado_pago', 'pendiente')
            ->first();

        if ($pagoExistente && !empty($pagoExistente->mp_preference_id)) {
            // Ya existe un pago, usar el existente
            $pagoId = $pagoExistente->id;
            $preferenciaId = $pagoExistente->mp_preference_id;
            
            // Obtener URL del botón de pago desde la preferencia existente
            $credenciales = $empresaConfigModel->obtenerCredencialesMercadoPago($empresaId);
            $webhookBaseUrl = rtrim(base_url(), '/');
            
            $mercadoPagoService = new \App\Services\MercadoPagoService(
                $credenciales['access_token'],
                $credenciales['public_key'],
                $credenciales['mode'],
                $webhookBaseUrl
            );
            
            $preferencia = $mercadoPagoService->obtenerPreferencia($preferenciaId);
            
            if ($preferencia) {
                $initPoint = ($credenciales['mode'] === 'sandbox') 
                    ? $preferencia->sandbox_init_point 
                    : $preferencia->init_point;
            } else {
                throw new \Exception('No se pudo obtener la preferencia de pago existente');
            }
        } else {
            // Crear nuevo pago (código existente)
            $empresaConfigModel = new \App\Models\EmpresaConfiguracion();
            if (!$empresaConfigModel->mercadoPagoHabilitado($empresaId)) {
                throw new \Exception('Mercado Pago no está configurado para esta empresa');
            }

            $pagoData = [
                'empresa_id' => $empresaId,
                'detalle_agenda_id' => $detalleAgendaId,
                'tipo_pago' => 'cita',
                'monto' => $plantilla->monto,
                'moneda' => $plantilla->moneda,
                'estado_pago' => 'pendiente',
                'observaciones' => 'Pago generado automáticamente al agendar cita. Plantilla: ' . $plantilla->titulo
            ];

            $pagoId = $pagoModel->insert($pagoData);
            
            if (!$pagoId) {
                throw new \Exception('Error al crear el registro de pago');
            }

            $credenciales = $empresaConfigModel->obtenerCredencialesMercadoPago($empresaId);
            $webhookBaseUrl = rtrim(base_url(), '/');
            
            $mercadoPagoService = new \App\Services\MercadoPagoService(
                $credenciales['access_token'],
                $credenciales['public_key'],
                $credenciales['mode'],
                $webhookBaseUrl
            );

            $preferenciaData = [
                'title' => $plantilla->titulo,
                'description' => $plantilla->descripcion ?? 'Pago de consulta nutricional',
                'unit_price' => $plantilla->monto,
                'quantity' => 1,
                'currency' => $plantilla->moneda,
                'payer_email' => $paciente->email,
                'payer_name' => $paciente->nombre ?? '',
                'payer_surname' => $paciente->apellido ?? '',
                'external_reference' => (string)$pagoId,
                'statement_descriptor' => 'NextLine Nutrición'
            ];

            $preferencia = $mercadoPagoService->crearPreferencia($preferenciaData);

            $pagoModel->update($pagoId, [
                'mp_preference_id' => $preferencia['preference_id']
            ]);

            $initPoint = ($credenciales['mode'] === 'sandbox') 
                ? $preferencia['sandbox_init_point'] 
                : $preferencia['init_point'];
        }

        // Enviar email con el botón de pago
        $this->enviarEmailBotonPago($paciente, $cita, $plantilla, $initPoint);

        log_message('info', 'Pago creado y botón enviado por email. Pago ID: ' . $pagoId . ', Email: ' . $paciente->email);
    }

    /**
     * Enviar email con botón de pago al paciente
     */
    private function enviarEmailBotonPago($paciente, $cita, $plantilla, $botonUrl)
    {
        $fechaFormateada = $cita->fecha ?? date('d-m-Y');
        $horaInicio = $cita->hora_inicio ? date('H:i', strtotime($cita->hora_inicio)) : '';
        $nombrePaciente = trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? ''));
        $montoFormateado = number_format($plantilla->monto, 0, ',', '.') . ' ' . $plantilla->moneda;

        // Crear el mensaje HTML
        $mensaje = view('emails/boton_pago_cita', [
            'nombrePaciente' => $nombrePaciente,
            'fecha' => $fechaFormateada,
            'horaInicio' => $horaInicio,
            'titulo' => $plantilla->titulo,
            'monto' => $montoFormateado,
            'botonUrl' => $botonUrl,
            'baseUrl' => base_url()
        ]);

        // Enviar email
        $email = Services::email();
        $email->setFrom(env('email.fromEmail', 'noreply@example.com'), env('email.fromName', 'NextLine Nutrición'));
        $email->setTo($paciente->email);
        $email->setSubject('💳 Pago de Consulta - ' . $plantilla->titulo);
        $email->setMessage($mensaje);

        if ($email->send()) {
            log_message('info', 'Email con botón de pago enviado a: ' . $paciente->email);
            return true;
        } else {
            log_message('error', 'Error al enviar email con botón de pago: ' . $email->printDebugger(['headers']));
            return false;
        }
    }

    /**
     * Enviar notificación de cancelación de cita al nutricionista por email
     */
    private function enviarEmailCancelacionNutricionista($cita)
    {
        log_message('info', '========================================');
        log_message('info', 'ENVIAR EMAIL CANCELACIÓN NUTRICIONISTA - INICIO');
        log_message('info', '========================================');
        
        if (!$cita) {
            log_message('error', 'ENVIAR EMAIL CANCELACIÓN: cita es NULL');
            log_message('info', '========================================');
            return false;
        }
        
        log_message('info', 'ENVIAR EMAIL CANCELACIÓN: Verificando datos de cita');
        log_message('info', '  - Cita ID: ' . ($cita->id ?? 'N/A'));
        log_message('info', '  - Usuario ID: ' . ($cita->usuario_id ?? 'N/A'));
        log_message('info', '  - Nutricionista nombre: ' . ($cita->nutricionista_nombre ?? 'N/A') . ' ' . ($cita->nutricionista_apellido ?? 'N/A'));
        log_message('info', '  - Nutricionista email: ' . ($cita->nutricionista_email ?? 'VACÍO'));
        
        if (empty($cita->nutricionista_email)) {
            log_message('error', 'ENVIAR EMAIL CANCELACIÓN: nutricionista_email está vacío');
            log_message('error', 'ENVIAR EMAIL CANCELACIÓN: Datos completos de cita: ' . json_encode([
                'id' => $cita->id ?? 'N/A',
                'usuario_id' => $cita->usuario_id ?? 'N/A',
                'nutricionista_nombre' => $cita->nutricionista_nombre ?? 'N/A',
                'nutricionista_apellido' => $cita->nutricionista_apellido ?? 'N/A',
                'nutricionista_email' => $cita->nutricionista_email ?? 'VACÍO',
                'fecha' => $cita->fecha ?? 'N/A',
                'hora_inicio' => $cita->hora_inicio ?? 'N/A'
            ]));
            log_message('info', '========================================');
            return false;
        }

        log_message('info', 'ENVIAR EMAIL CANCELACIÓN: Preparando datos para el email');
        log_message('info', '  - Email destino: ' . $cita->nutricionista_email);

        // Preparar datos para el email
        $fechaFormateada = $cita->fecha; // Ya está en DD-MM-YYYY
        $horaInicio = date('H:i', strtotime($cita->hora_inicio));
        $horaFin = date('H:i', strtotime($cita->hora_fin));
        $nombrePaciente = trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? ''));
        $nombreNutricionista = trim(($cita->nutricionista_nombre ?? '') . ' ' . ($cita->nutricionista_apellido ?? ''));
        $modalidadNombre = $cita->modalidad_nombre ?? 'No definida';
        $tipoConsulta = ucfirst(str_replace('_', ' ', $cita->tipo_consulta ?? 'control'));

        log_message('info', 'ENVIAR EMAIL CANCELACIÓN: Datos preparados');
        log_message('info', '  - Paciente: ' . $nombrePaciente);
        log_message('info', '  - Nutricionista: ' . $nombreNutricionista);
        log_message('info', '  - Fecha: ' . $fechaFormateada);
        log_message('info', '  - Hora: ' . $horaInicio . ' - ' . $horaFin);
        log_message('info', '  - Modalidad: ' . $modalidadNombre);
        log_message('info', '  - Tipo consulta: ' . $tipoConsulta);

        // Crear el mensaje HTML
        log_message('info', 'ENVIAR EMAIL CANCELACIÓN: Generando vista del email');
        try {
            $mensaje = view('emails/cancelacion_cita_nutricionista', [
                'nombreNutricionista' => $nombreNutricionista,
                'nombrePaciente' => $nombrePaciente,
                'fecha' => $fechaFormateada,
                'horaInicio' => $horaInicio,
                'horaFin' => $horaFin,
                'modalidad' => $modalidadNombre,
                'tipoConsulta' => $tipoConsulta,
                'motivo' => $cita->motivo ?? '',
                'baseUrl' => base_url()
            ]);
            log_message('info', 'ENVIAR EMAIL CANCELACIÓN: Vista generada exitosamente (longitud: ' . strlen($mensaje) . ' caracteres)');
        } catch (\Exception $e) {
            log_message('error', 'ENVIAR EMAIL CANCELACIÓN: Error al generar vista: ' . $e->getMessage());
            log_message('error', 'ENVIAR EMAIL CANCELACIÓN: Stack trace: ' . $e->getTraceAsString());
            log_message('info', '========================================');
            return false;
        }

        // Enviar email
        log_message('info', 'ENVIAR EMAIL CANCELACIÓN: Configurando email');
        $email = Services::email();
        $fromEmail = env('email.fromEmail', 'noreply@example.com');
        $fromName = env('email.fromName', 'Sistema de Agenda');
        
        log_message('info', 'ENVIAR EMAIL CANCELACIÓN: From: ' . $fromEmail . ' (' . $fromName . ')');
        log_message('info', 'ENVIAR EMAIL CANCELACIÓN: To: ' . $cita->nutricionista_email);
        
        $email->setFrom($fromEmail, $fromName);
        $email->setTo($cita->nutricionista_email);
        
        $subject = '❌ Cita Cancelada - ' . $nombrePaciente . ' - ' . $fechaFormateada . ' a las ' . $horaInicio;
        $email->setSubject($subject);
        log_message('info', 'ENVIAR EMAIL CANCELACIÓN: Subject: ' . $subject);
        
        $email->setMessage($mensaje);

        log_message('info', 'ENVIAR EMAIL CANCELACIÓN: Intentando enviar email...');
        if ($email->send()) {
            log_message('info', 'ENVIAR EMAIL CANCELACIÓN: ✅ Email enviado exitosamente al nutricionista: ' . $cita->nutricionista_email);
            log_message('info', '========================================');
            return true;
        } else {
            $debugInfo = $email->printDebugger(['headers']);
            log_message('error', 'ENVIAR EMAIL CANCELACIÓN: ❌ Error al enviar email');
            log_message('error', 'ENVIAR EMAIL CANCELACIÓN: Debug info: ' . $debugInfo);
            log_message('info', '========================================');
            return false;
        }
    }

    /**
     * Enviar confirmación de cita por WhatsApp
     * @param int $detalleAgendaId ID del detalle de agenda
     * @param int $pacienteId ID del paciente
     * @param string|null $meetLink Enlace de Google Meet (opcional, para citas online)
     */
    private function enviarWhatsAppConfirmacion($detalleAgendaId, $pacienteId, $meetLink = null)
    {
        log_message('error', '========================================');
        log_message('error', 'ENVIAR WHATSAPP CONFIRMACIÓN - INICIO');
        log_message('error', 'detalleAgendaId=' . $detalleAgendaId . ', pacienteId=' . $pacienteId . ', meetLink=' . ($meetLink ? 'SÍ' : 'NO'));
        log_message('error', '========================================');
        
        // Verificar si WhatsApp está configurado
        $whatsappProvider = env('WHATSAPP_PROVIDER');
        log_message('error', 'WHATSAPP CONFIRMACIÓN: WhatsApp Provider configurado: ' . ($whatsappProvider ? $whatsappProvider : 'NO'));
        
        if (empty($whatsappProvider)) {
            log_message('error', 'WHATSAPP CONFIRMACIÓN: WhatsApp no configurado, omitiendo envío');
            return false;
        }

        try {
            log_message('error', 'WHATSAPP CONFIRMACIÓN: Instanciando WhatsAppService');
            $whatsappService = new WhatsAppService();
            
            log_message('error', 'WHATSAPP CONFIRMACIÓN: Llamando a enviarConfirmacionCita()');
            $resultado = $whatsappService->enviarConfirmacionCita($detalleAgendaId, $pacienteId, $meetLink);
            
            log_message('error', 'WHATSAPP CONFIRMACIÓN: Resultado recibido. success=' . ($resultado['success'] ? 'SÍ' : 'NO'));
            
            if ($resultado['success']) {
                log_message('error', 'WHATSAPP CONFIRMACIÓN: WhatsApp de confirmación enviado exitosamente para cita ID: ' . $detalleAgendaId . ($meetLink ? ' (con enlace Meet)' : ''));
                log_message('error', '========================================');
                return true;
            } else {
                log_message('error', 'WHATSAPP CONFIRMACIÓN: Error al enviar WhatsApp: ' . ($resultado['error'] ?? 'Error desconocido'));
                log_message('error', '========================================');
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', 'WHATSAPP CONFIRMACIÓN: Excepción al enviar WhatsApp: ' . $e->getMessage());
            log_message('error', 'WHATSAPP CONFIRMACIÓN: Stack trace: ' . $e->getTraceAsString());
            log_message('error', '========================================');
            return false;
        }
    }

    /**
     * Confirmar cita desde el email (público, sin autenticación)
     */
    public function confirmarDesdeEmail()
    {
        error_log('========================================');
        error_log('CONFIRMAR DESDE EMAIL - MÉTODO EJECUTADO (error_log)');
        error_log('Timestamp: ' . date('Y-m-d H:i:s'));
        error_log('URL: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'N/A'));
        error_log('========================================');
        
        log_message('error', '========================================');
        log_message('error', 'CONFIRMAR DESDE EMAIL - MÉTODO EJECUTADO');
        log_message('error', 'Timestamp: ' . date('Y-m-d H:i:s'));
        log_message('error', 'URL: ' . current_url());
        log_message('error', '========================================');
        
        $token = $this->request->getGet('token');
        log_message('info', 'CONFIRMAR DESDE EMAIL: Token recibido=' . ($token ? substr($token, 0, 30) . '...' : 'NO'));
        
        if (!$token) {
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Token inválido o faltante.'
            ]);
        }

        // Decodificar y validar token
        $datos = base64_decode($token);
        if (!$datos) {
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Token inválido o corrupto.'
            ]);
        }

        // Validar que el token tenga el formato correcto
        $partes = explode('|', $datos);
        if (count($partes) !== 3) {
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Token con formato inválido.'
            ]);
        }

        list($detalleAgendaId, $pacienteId, $hash) = $partes;
        
        // Validar que los IDs sean numéricos
        if (!is_numeric($detalleAgendaId) || !is_numeric($pacienteId)) {
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Token contiene datos inválidos.'
            ]);
        }
        
        // Validar hash
        $hashEsperado = hash('sha256', $detalleAgendaId . $pacienteId . 'confirmar');
        if ($hash !== $hashEsperado) {
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Token inválido o manipulado.'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            
            // Verificar que la cita existe y pertenece al paciente
            $cita = $db->table('detalle_agenda')
                ->where('id', $detalleAgendaId)
                ->where('paciente_id', $pacienteId)
                ->get()
                ->getRow();

            if (!$cita) {
                return view('emails/respuesta_cita', [
                    'exito' => false,
                    'mensaje' => 'La cita no existe o ya fue cancelada.'
                ]);
            }

            // Verificar estados que no permiten confirmación
            if ($cita->estado_cita === 'confirmada' || $cita->estado_cita === 'agendada') {
                return view('emails/respuesta_cita', [
                    'exito' => true,
                    'mensaje' => $cita->estado_cita === 'agendada' 
                        ? 'La cita ya está agendada y pagada.' 
                        : 'La cita ya estaba confirmada previamente.'
                ]);
            }
            
            // Usar transacción para evitar race conditions en doble clic
            $db->transStart();
            
            // Re-verificar el estado dentro de la transacción usando SQL directo con FOR UPDATE
            // Esto bloquea la fila para evitar race conditions
            $sql = "SELECT * FROM detalle_agenda 
                    WHERE id = ? AND paciente_id = ? 
                    FOR UPDATE";
            $citaActualizada = $db->query($sql, [$detalleAgendaId, $pacienteId])->getRow();
            
            if (!$citaActualizada) {
                $db->transRollback();
                return view('emails/respuesta_cita', [
                    'exito' => false,
                    'mensaje' => 'La cita no existe o ya fue cancelada.'
                ]);
            }
            
            // Verificar nuevamente el estado después del bloqueo
            if ($citaActualizada->estado_cita === 'confirmada' || $citaActualizada->estado_cita === 'agendada') {
                $db->transRollback();
                return view('emails/respuesta_cita', [
                    'exito' => true,
                    'mensaje' => $citaActualizada->estado_cita === 'agendada' 
                        ? 'La cita ya está agendada y pagada.' 
                        : 'La cita ya estaba confirmada previamente.'
                ]);
            }
            
            // Verificar que la cita esté en estado 'en_proceso' (con botón de pago) o 'pendiente'
            $tienePago = false;
            if ($citaActualizada->estado_cita === 'en_proceso') {
                // Cita con botón de pago: cambiar a 'pendiente' (esperando que el paciente pague)
                // Usar UPDATE con WHERE para asegurar atomicidad (solo actualiza si el estado es 'en_proceso')
                $actualizado = $db->table('detalle_agenda')
                    ->where('id', $detalleAgendaId)
                    ->where('estado_cita', 'en_proceso') // Condición adicional para atomicidad
                    ->update([
                        'estado_cita' => 'pendiente',
                        'fecha_confirmacion' => date('Y-m-d H:i:s')
                    ]);
                
                if (!$actualizado) {
                    // Si no se actualizó, significa que otro proceso ya cambió el estado
                    $db->transRollback();
                    return view('emails/respuesta_cita', [
                        'exito' => false,
                        'mensaje' => 'El estado de la cita cambió. Por favor, intenta nuevamente.'
                    ]);
                }
                
                // Buscar si hay un pago pendiente para esta cita
                $pagoModel = new \App\Models\Pago();
                $pago = $pagoModel->where('detalle_agenda_id', $detalleAgendaId)
                    ->where('estado_pago', 'pendiente')
                    ->first();
                
                log_message('error', 'CONFIRMAR CITA: Buscando pago para detalle_agenda_id=' . $detalleAgendaId);
                log_message('error', 'CONFIRMAR CITA: Pago encontrado: ' . ($pago ? 'SÍ (ID: ' . $pago->id . ', preference_id: ' . ($pago->mp_preference_id ?? 'NULL') . ')' : 'NO'));
                
                if ($pago && !empty($pago->mp_preference_id)) {
                    $tienePago = true;
                    log_message('error', 'CONFIRMAR CITA: Hay pago pendiente, procediendo a enviar email con botón de pago');
                    // Hay un pago pendiente, enviar email con botón de pago
                    try {
                        // Obtener empresa_id directamente del pago (ya está guardado cuando se crea)
                        $empresaId = $pago->empresa_id ?? null;
                        
                        if (!$empresaId) {
                            // Si no está en el pago, obtenerlo desde el usuario de la cita (nutricionista)
                            $usuarioModel = new \App\Models\Usuario();
                            $usuario = $usuarioModel->find($citaActualizada->usuario_id);
                            
                            // El modelo Usuario retorna array, no objeto
                            $empresaId = is_array($usuario) ? ($usuario['empresa_id'] ?? null) : ($usuario->empresa_id ?? null);
                            
                            if (!$empresaId) {
                                log_message('error', 'No se pudo obtener empresa_id. Pago ID: ' . $pago->id . ', Usuario ID: ' . $citaActualizada->usuario_id);
                                throw new \Exception('No se pudo obtener la empresa. Pago ID: ' . $pago->id);
                            }
                            
                            log_message('error', 'CONFIRMAR CITA: Empresa ID obtenida desde usuario de la cita: ' . $empresaId);
                        } else {
                            log_message('error', 'CONFIRMAR CITA: Empresa ID obtenida desde pago: ' . $empresaId);
                        }
                        
                        // Obtener datos del paciente
                        $pacienteModel = new \App\Models\Paciente();
                        $paciente = $pacienteModel->find($pacienteId);
                        
                        if (!$paciente || empty($paciente->email)) {
                            throw new \Exception('Paciente no encontrado o sin email');
                        }
                        
                        // Obtener datos completos de la cita
                        // IMPORTANTE: La hora está en detalle_agenda (da.hora_inicio), no en agenda (a.hora_inicio)
                        $citaCompleta = $db->table('detalle_agenda da')
                            ->select('da.*, a.fecha, da.hora_inicio, da.hora_fin')
                            ->join('agenda a', 'a.id = da.agenda_id', 'left')
                            ->where('da.id', $detalleAgendaId)
                            ->get()
                            ->getRow();
                        
                        // Obtener credenciales de Mercado Pago
                        $empresaConfigModel = new \App\Models\EmpresaConfiguracion();
                        $credenciales = $empresaConfigModel->obtenerCredencialesMercadoPago($empresaId);
                        
                        if (!$credenciales || !$credenciales['habilitado']) {
                            throw new \Exception('Mercado Pago no está configurado para esta empresa');
                        }
                        
                        $webhookBaseUrl = rtrim(base_url(), '/');
                        $mercadoPagoService = new \App\Services\MercadoPagoService(
                            $credenciales['access_token'],
                            $credenciales['public_key'],
                            $credenciales['mode'],
                            $webhookBaseUrl
                        );
                        
                        log_message('error', 'CONFIRMAR CITA: Obteniendo preferencia de Mercado Pago. Preference ID: ' . $pago->mp_preference_id);
                        $preferencia = $mercadoPagoService->obtenerPreferencia($pago->mp_preference_id);
                        
                        if ($preferencia) {
                            log_message('error', 'CONFIRMAR CITA: Preferencia obtenida. Modo: ' . $credenciales['mode']);
                            
                            // Acceder a las propiedades de la preferencia
                            $initPoint = null;
                            if ($credenciales['mode'] === 'sandbox') {
                                $initPoint = $preferencia->sandbox_init_point ?? null;
                                log_message('error', 'CONFIRMAR CITA: sandbox_init_point: ' . ($initPoint ?? 'NULL'));
                            } else {
                                $initPoint = $preferencia->init_point ?? null;
                                log_message('error', 'CONFIRMAR CITA: init_point: ' . ($initPoint ?? 'NULL'));
                            }
                            
                            if (empty($initPoint)) {
                                log_message('error', 'CONFIRMAR CITA: init_point está vacío. Preferencia ID: ' . ($preferencia->id ?? 'N/A'));
                                throw new \Exception('No se pudo obtener la URL del botón de pago (init_point vacío)');
                            }
                            
                            // Crear objeto plantilla temporal con datos del pago
                            $plantillaTemporal = (object)[
                                'titulo' => 'Pago de Consulta',
                                'descripcion' => $pago->observaciones ?? 'Pago de consulta nutricional',
                                'monto' => $pago->monto,
                                'moneda' => $pago->moneda
                            ];
                            
                            log_message('error', 'CONFIRMAR CITA: Enviando email con botón de pago a: ' . $paciente->email . ', URL: ' . substr($initPoint, 0, 50) . '...');
                            // Enviar email con botón de pago
                            $this->enviarEmailBotonPago($paciente, $citaCompleta, $plantillaTemporal, $initPoint);
                            
                            log_message('error', 'CONFIRMAR CITA: Email con botón de pago enviado exitosamente. Pago ID: ' . $pago->id);
                        } else {
                            log_message('error', 'CONFIRMAR CITA: No se pudo obtener la preferencia de pago');
                            throw new \Exception('No se pudo obtener la preferencia de pago desde Mercado Pago');
                        }
                    } catch (\Exception $e) {
                        log_message('error', 'CONFIRMAR CITA: Error al enviar email con botón de pago: ' . $e->getMessage());
                        log_message('error', 'CONFIRMAR CITA: Stack trace: ' . $e->getTraceAsString());
                        // No fallar la confirmación si el email falla, pero loguear el error completo
                    }
                } else {
                    log_message('error', 'CONFIRMAR CITA: No hay pago pendiente o no tiene preference_id. Pago: ' . ($pago ? 'existe pero sin preference_id' : 'no existe'));
                }
            } elseif ($citaActualizada->estado_cita === 'pendiente') {
                // Estado 'pendiente': verificar si hay pago pendiente antes de confirmar
                $pagoModel = new \App\Models\Pago();
                $pagoPendiente = $pagoModel->where('detalle_agenda_id', $detalleAgendaId)
                    ->where('estado_pago', 'pendiente')
                    ->first();
                
                if ($pagoPendiente) {
                    // Si hay pago pendiente, NO se debe confirmar la cita
                    // El estado debe permanecer en 'pendiente' hasta que se complete el pago
                    $db->transRollback();
                    return view('emails/respuesta_cita', [
                        'exito' => false,
                        'mensaje' => 'Esta cita tiene un pago pendiente. Por favor, completa el pago antes de confirmar. Revisa tu correo para el enlace de pago.'
                    ]);
                }
                
                // Solo si NO hay pago pendiente, cambiar a 'confirmada'
                // Usar UPDATE con WHERE para asegurar atomicidad
                $actualizado = $db->table('detalle_agenda')
                    ->where('id', $detalleAgendaId)
                    ->where('estado_cita', 'pendiente') // Condición adicional para atomicidad
                    ->update([
                        'estado_cita' => 'confirmada',
                        'fecha_confirmacion' => date('Y-m-d H:i:s')
                    ]);
                
                if (!$actualizado) {
                    // Si no se actualizó, significa que otro proceso ya cambió el estado
                    $db->transRollback();
                    return view('emails/respuesta_cita', [
                        'exito' => false,
                        'mensaje' => 'El estado de la cita cambió. Por favor, intenta nuevamente.'
                    ]);
                }
            } else {
                $db->transRollback();
                return view('emails/respuesta_cita', [
                    'exito' => false,
                    'mensaje' => 'Esta cita no puede ser confirmada porque su estado actual es: ' . ($citaActualizada->estado_cita ?? 'desconocido') . '.'
                ]);
            }
            
            // Confirmar la transacción
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return view('emails/respuesta_cita', [
                    'exito' => false,
                    'mensaje' => 'Ocurrió un error al procesar la confirmación. Por favor, intenta nuevamente.'
                ]);
            }

            // Obtener usuario_id del nutricionista para crear el evento en su calendario
            $usuarioId = $citaActualizada->usuario_id ?? null;

            // Obtener configuraciones del nutricionista
            $configuracionModel = new \App\Models\EmpresaConfiguracion();
            $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($usuarioId);

            $meetLink = null; // Variable para almacenar el enlace de Meet si se crea

            // Crear evento en el calendario del nutricionista cuando el paciente confirma
            // IMPORTANTE: Solo crear el evento si NO hay botón de pago
            // Si hay botón de pago, el evento se creará cuando el pago se apruebe
            if (!$tienePago && $usuarioId && ($configuracion['crear_evento_calendario'] ?? 1)) {
                try {
                    log_message('info', 'Creando evento en calendario al confirmar cita (sin botón de pago)');
                    $resultadoCalendario = $this->crearEventoCalendario($detalleAgendaId, $usuarioId);
                    if ($resultadoCalendario && isset($resultadoCalendario['meet_link'])) {
                        $meetLink = $resultadoCalendario['meet_link'];
                        log_message('info', 'Enlace de Google Meet obtenido: ' . $meetLink);
                    }
                } catch (\Exception $e) {
                    // No fallar la confirmación si el calendario falla, solo loguear
                    log_message('error', 'Error al crear evento en calendario desde confirmación email: ' . $e->getMessage());
                }
            } else {
                if ($tienePago) {
                    log_message('info', 'Evento en calendario NO creado al confirmar (hay botón de pago - se creará cuando se apruebe el pago)');
                } elseif (!($configuracion['crear_evento_calendario'] ?? 1)) {
                    log_message('info', 'Creación de evento en calendario deshabilitada en configuraciones del usuario ID: ' . $usuarioId);
                }
            }

            // Enviar WhatsApp de confirmación cuando el paciente confirma desde el email (solo si está habilitado)
            // Incluir el enlace de Meet si está disponible (para citas online)
            log_message('info', 'CONFIRMAR DESDE EMAIL: Verificando configuración de WhatsApp');
            log_message('info', 'CONFIRMAR DESDE EMAIL: enviar_whatsapp=' . ($configuracion['enviar_whatsapp'] ?? 'N/A') . ', usuarioId=' . ($usuarioId ?? 'N/A'));
            
            if ($configuracion['enviar_whatsapp'] ?? 1) {
                log_message('info', 'CONFIRMAR DESDE EMAIL: WhatsApp habilitado, procediendo a enviar');
                log_message('info', 'CONFIRMAR DESDE EMAIL: detalleAgendaId=' . $detalleAgendaId . ', pacienteId=' . $pacienteId . ', meetLink=' . ($meetLink ? 'SÍ' : 'NO'));
                try {
                    $this->enviarWhatsAppConfirmacion($detalleAgendaId, $pacienteId, $meetLink);
                } catch (\Exception $e) {
                    // No fallar la confirmación si WhatsApp falla, solo loguear
                    log_message('error', 'CONFIRMAR DESDE EMAIL: Excepción al enviar WhatsApp: ' . $e->getMessage());
                    log_message('error', 'CONFIRMAR DESDE EMAIL: Stack trace: ' . $e->getTraceAsString());
                }
            } else {
                log_message('info', 'CONFIRMAR DESDE EMAIL: WhatsApp deshabilitado en configuraciones del usuario ID: ' . $usuarioId);
            }

            // Mensaje final según si tiene pago o no
            $mensajeFinal = $tienePago 
                ? 'Cita confirmada. Revisa tu correo para completar el pago.'
                : '¡Cita confirmada exitosamente! Te esperamos en la fecha y hora acordada.';
            
            return view('emails/respuesta_cita', [
                'exito' => true,
                'mensaje' => $mensajeFinal
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al confirmar cita desde email: ' . $e->getMessage());
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Ocurrió un error al procesar la confirmación. Por favor, contacta con tu nutricionista.'
            ]);
        }
    }

    /**
     * Cancelar cita desde el email (público, sin autenticación)
     */
    public function cancelarDesdeEmail()
    {
        // Log inmediato con error_log también para asegurar que se ejecuta
        error_log('========================================');
        error_log('CANCELAR DESDE EMAIL - MÉTODO EJECUTADO (error_log)');
        error_log('Timestamp: ' . date('Y-m-d H:i:s'));
        error_log('URL: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'N/A'));
        error_log('GET params: ' . json_encode($_GET));
        error_log('========================================');
        
        log_message('info', '========================================');
        log_message('info', 'CANCELAR DESDE EMAIL - INICIO');
        log_message('info', '========================================');
        log_message('info', 'Timestamp: ' . date('Y-m-d H:i:s'));
        log_message('info', 'URL: ' . current_url());
        log_message('info', 'REQUEST_URI: ' . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
        log_message('info', 'GET completo: ' . json_encode($this->request->getGet()));
        
        $token = $this->request->getGet('token');
        log_message('info', 'Token recibido: ' . ($token ? substr($token, 0, 30) . '...' : 'NO'));
        error_log('Token recibido: ' . ($token ? substr($token, 0, 30) . '...' : 'NO'));
        
        if (!$token) {
            log_message('warning', 'CANCELAR DESDE EMAIL: Token faltante');
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Token inválido o faltante.'
            ]);
        }

        // Decodificar y validar token
        $datos = base64_decode($token);
        if (!$datos) {
            log_message('warning', 'CANCELAR DESDE EMAIL: Token no se pudo decodificar');
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Token inválido o corrupto.'
            ]);
        }

        // Validar que el token tenga el formato correcto
        $partes = explode('|', $datos);
        log_message('info', 'CANCELAR DESDE EMAIL: Partes del token: ' . count($partes));
        
        if (count($partes) !== 3) {
            log_message('warning', 'CANCELAR DESDE EMAIL: Token con formato inválido (partes: ' . count($partes) . ')');
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Token con formato inválido.'
            ]);
        }

        list($detalleAgendaId, $pacienteId, $hash) = $partes;
        log_message('info', 'CANCELAR DESDE EMAIL: detalleAgendaId=' . $detalleAgendaId . ', pacienteId=' . $pacienteId);
        
        // Validar que los IDs sean numéricos
        if (!is_numeric($detalleAgendaId) || !is_numeric($pacienteId)) {
            log_message('warning', 'CANCELAR DESDE EMAIL: IDs no numéricos');
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Token contiene datos inválidos.'
            ]);
        }
        
        // Validar hash
        $hashEsperado = hash('sha256', $detalleAgendaId . $pacienteId . 'cancelar');
        if ($hash !== $hashEsperado) {
            log_message('warning', 'CANCELAR DESDE EMAIL: Hash inválido. Esperado: ' . substr($hashEsperado, 0, 20) . '..., Recibido: ' . substr($hash, 0, 20) . '...');
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Token inválido o manipulado.'
            ]);
        }

        log_message('info', 'CANCELAR DESDE EMAIL: Token validado correctamente');

        try {
            $db = \Config\Database::connect();
            
            // Verificar que la cita existe y pertenece al paciente
            log_message('info', 'CANCELAR DESDE EMAIL: Buscando cita detalleAgendaId=' . $detalleAgendaId . ', pacienteId=' . $pacienteId);
            $cita = $db->table('detalle_agenda')
                ->where('id', $detalleAgendaId)
                ->where('paciente_id', $pacienteId)
                ->get()
                ->getRow();

            if (!$cita) {
                log_message('warning', 'CANCELAR DESDE EMAIL: Cita no encontrada');
                return view('emails/respuesta_cita', [
                    'exito' => false,
                    'mensaje' => 'La cita no existe o ya fue cancelada.'
                ]);
            }

            log_message('info', 'CANCELAR DESDE EMAIL: Cita encontrada. Estado actual: ' . ($cita->estado_cita ?? 'N/A'));

            // Verificar que la cita no esté ya cancelada
            if ($cita->estado_cita === 'cancelada') {
                log_message('info', 'CANCELAR DESDE EMAIL: Cita ya estaba cancelada previamente');
                return view('emails/respuesta_cita', [
                    'exito' => true,
                    'mensaje' => 'La cita ya estaba cancelada previamente.'
                ]);
            }

            // Obtener información completa de la cita ANTES de cancelarla (incluyendo datos del paciente para WhatsApp)
            log_message('info', 'CANCELAR DESDE EMAIL: Obteniendo información completa de la cita');
            $citaCompleta = $db->table('detalle_agenda da')
                ->select('da.id, da.hora_inicio, da.hora_fin, da.modalidad_id, da.tipo_consulta, da.motivo, da.calendar_event_id, da.usuario_id,
                          a.fecha,
                          p.id as paciente_id_db, p.nombre, p.apellido, p.email as paciente_email, p.telefono as paciente_telefono,
                          u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido, u.correo as nutricionista_email,
                          ma.nombre as modalidad_nombre')
                ->join('agenda a', 'a.id = da.agenda_id', 'left')
                ->join('pacientes p', 'p.id = da.paciente_id', 'left')
                ->join('usuario u', 'u.id = da.usuario_id', 'left') // Cambiar a da.usuario_id para obtener el nutricionista correcto
                ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
                ->where('da.id', $detalleAgendaId)
                ->where('da.paciente_id', $pacienteId)
                ->get()
                ->getRow();

            if ($citaCompleta) {
                log_message('info', 'CANCELAR DESDE EMAIL: Información completa obtenida');
                log_message('info', '  - Nutricionista: ' . ($citaCompleta->nutricionista_nombre ?? 'N/A') . ' ' . ($citaCompleta->nutricionista_apellido ?? 'N/A'));
                log_message('info', '  - Email nutricionista: ' . ($citaCompleta->nutricionista_email ?? 'NO'));
                log_message('info', '  - Usuario ID: ' . ($citaCompleta->usuario_id ?? 'N/A'));
                log_message('info', '  - Paciente: ' . ($citaCompleta->nombre ?? 'N/A') . ' ' . ($citaCompleta->apellido ?? 'N/A'));
                log_message('info', '  - Fecha: ' . ($citaCompleta->fecha ?? 'N/A'));
            } else {
                log_message('warning', 'CANCELAR DESDE EMAIL: No se pudo obtener información completa de la cita');
            }

            // Cancelar la cita y liberar el horario
            log_message('info', 'CANCELAR DESDE EMAIL: Cancelando cita en base de datos');
            $db->table('detalle_agenda')
                ->where('id', $detalleAgendaId)
                ->update([
                    'estado_cita' => 'cancelada',
                    'fecha_cancelacion' => date('Y-m-d H:i:s'),
                    'paciente_id' => null,
                    'estado' => 1 // Disponible nuevamente
                ]);
            log_message('info', 'CANCELAR DESDE EMAIL: Cita cancelada exitosamente en BD');

            // Obtener configuraciones del nutricionista
            $configuracionModel = new \App\Models\EmpresaConfiguracion();
            $usuarioId = $citaCompleta->usuario_id ?? null;
            $configuracion = $usuarioId ? $configuracionModel->obtenerConfiguracionPorUsuario($usuarioId) : [];

            // Eliminar evento del calendario si existe (solo si está habilitado)
            if ($citaCompleta && ($configuracion['crear_evento_calendario'] ?? 1)) {
                try {
                    log_message('info', 'CANCELAR DESDE EMAIL: Intentando eliminar evento del calendario');
                    $this->eliminarEventoCalendario($detalleAgendaId, $citaCompleta->usuario_id);
                    log_message('info', 'CANCELAR DESDE EMAIL: Evento del calendario eliminado (o no existía)');
                } catch (\Exception $e) {
                    // No fallar la cancelación si la eliminación del calendario falla, solo loguear
                    log_message('error', 'CANCELAR DESDE EMAIL: Error al eliminar evento del calendario: ' . $e->getMessage());
                }
            }

            // Enviar WhatsApp al paciente cuando se cancela desde el email (solo si está habilitado)
            // IMPORTANTE: Usar pacienteId y datos de citaCompleta porque paciente_id ya fue puesto en null
            if ($configuracion['enviar_whatsapp'] ?? 1) {
                try {
                    log_message('info', 'CANCELAR DESDE EMAIL: Enviando WhatsApp de cancelación al paciente');
                    log_message('info', 'CANCELAR DESDE EMAIL: pacienteId=' . $pacienteId . ', telefono=' . ($citaCompleta->paciente_telefono ?? 'N/A'));
                    
                    // Verificar que tenemos teléfono del paciente
                    if (empty($citaCompleta->paciente_telefono)) {
                        log_message('warning', 'CANCELAR DESDE EMAIL: No se puede enviar WhatsApp - paciente sin teléfono');
                    } else {
                        $whatsappService = new WhatsAppService();
                        $resultado = $whatsappService->enviarCancelacionCita($detalleAgendaId, $pacienteId);
                        if ($resultado['success']) {
                            log_message('info', 'CANCELAR DESDE EMAIL: WhatsApp de cancelación enviado al paciente exitosamente');
                        } else {
                            log_message('warning', 'CANCELAR DESDE EMAIL: Error al enviar WhatsApp: ' . ($resultado['error'] ?? 'Error desconocido'));
                        }
                    }
                } catch (\Exception $e) {
                    // No fallar la cancelación si WhatsApp falla, solo loguear
                    log_message('error', 'CANCELAR DESDE EMAIL: Excepción al enviar WhatsApp de cancelación: ' . $e->getMessage());
                    log_message('error', 'CANCELAR DESDE EMAIL: Stack trace: ' . $e->getTraceAsString());
                }
            } else {
                log_message('info', 'CANCELAR DESDE EMAIL: WhatsApp deshabilitado en configuraciones del usuario ID: ' . ($citaCompleta->usuario_id ?? 'N/A'));
            }

            // Enviar notificación por email al nutricionista sobre el rechazo
            log_message('info', 'CANCELAR DESDE EMAIL: Verificando condiciones para enviar email al nutricionista');
            log_message('info', '  - citaCompleta existe: ' . ($citaCompleta ? 'SÍ' : 'NO'));
            log_message('info', '  - nutricionista_email: ' . (!empty($citaCompleta->nutricionista_email) ? $citaCompleta->nutricionista_email : 'VACÍO'));
            
            if ($citaCompleta && !empty($citaCompleta->nutricionista_email)) {
                log_message('info', 'CANCELAR DESDE EMAIL: Condiciones cumplidas, procediendo a enviar email');
                try {
                    log_message('info', 'CANCELAR DESDE EMAIL: Configuración obtenida. enviar_email=' . ($configuracion['enviar_email'] ?? 'N/A'));
                    
                    if ($configuracion['enviar_email'] ?? 1) {
                        log_message('info', 'CANCELAR DESDE EMAIL: Email habilitado, llamando a enviarEmailCancelacionNutricionista()');
                        $resultado = $this->enviarEmailCancelacionNutricionista($citaCompleta);
                        log_message('info', 'CANCELAR DESDE EMAIL: Resultado de enviarEmailCancelacionNutricionista: ' . ($resultado ? 'ÉXITO' : 'FALLO'));
                    } else {
                        log_message('info', 'CANCELAR DESDE EMAIL: Email deshabilitado en configuraciones del usuario ID: ' . $citaCompleta->usuario_id);
                    }
                } catch (\Exception $e) {
                    // No fallar la cancelación si el email falla, solo loguear
                    log_message('error', 'CANCELAR DESDE EMAIL: Excepción al enviar email de cancelación al nutricionista: ' . $e->getMessage());
                    log_message('error', 'CANCELAR DESDE EMAIL: Stack trace: ' . $e->getTraceAsString());
                }
            } else {
                if (!$citaCompleta) {
                    log_message('warning', 'CANCELAR DESDE EMAIL: No se enviará email porque citaCompleta es NULL');
                } else {
                    log_message('warning', 'CANCELAR DESDE EMAIL: No se enviará email porque nutricionista_email está vacío');
                }
            }

            log_message('info', 'CANCELAR DESDE EMAIL: Proceso completado exitosamente');
            log_message('info', '========================================');

            return view('emails/respuesta_cita', [
                'exito' => true,
                'mensaje' => 'Cita cancelada exitosamente. El horario ha sido liberado.'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'CANCELAR DESDE EMAIL: Excepción general: ' . $e->getMessage());
            log_message('error', 'CANCELAR DESDE EMAIL: Stack trace: ' . $e->getTraceAsString());
            log_message('info', '========================================');
            return view('emails/respuesta_cita', [
                'exito' => false,
                'mensaje' => 'Ocurrió un error al procesar la cancelación. Por favor, contacta con tu nutricionista.'
            ]);
        }
    }

    /**
     * Vista de consulta en curso
     */
    public function consulta()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        // Obtener ID de la consulta desde GET
        $detalleAgendaId = $this->request->getGet('id');
        if (!$detalleAgendaId) {
            return redirect()->to(base_url('dashboard/agenda/calendario'))
                ->with('error', 'Debe seleccionar una cita para iniciar la consulta');
        }
        $detalleAgendaId = (int) $detalleAgendaId;

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Obtener información completa de la consulta
        $cita = $db->table('detalle_agenda da')
            ->select('da.*, a.fecha as fecha_agenda,
                      p.nombre, p.apellido, p.telefono, p.email, p.rut_dni, p.fecha_nacimiento, p.genero,
                      ma.nombre as modalidad_nombre')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
            ->where('da.id', $detalleAgendaId)
            ->where('da.usuario_id', $usuario_id)
            ->get()
            ->getRow();

        if (!$cita || !$cita->paciente_id) {
            return redirect()->to(base_url('dashboard/agenda/calendario'))
                ->with('error', 'Cita no encontrada o no tiene paciente asignado');
        }

        $data['cita'] = $cita;
        $data['fecha'] = $cita->fecha ?: $cita->fecha_agenda;

        // Buscar si ya existe un registro de historial clínico para esta cita (incluir soft-deleted para mostrar datos)
        $historialModel = new HistorialClinico();
        $historialExistente = $historialModel->withDeleted()
            ->where('detalle_agenda_id', $detalleAgendaId)
            ->where('paciente_id', $cita->paciente_id)
            ->first();

        // Pasar historial como array para que la vista pueda hacer json_encode sin fallos (objetos/fechas)
        if ($historialExistente) {
            $historialArray = is_object($historialExistente) ? (array) $historialExistente : $historialExistente;
            foreach ($historialArray as $k => $v) {
                if ($v instanceof \DateTimeInterface) {
                    $historialArray[$k] = $v->format('Y-m-d H:i:s');
                }
            }
            $data['historial'] = $historialArray;
        } else {
            $data['historial'] = [];
        }

        // Cargar exámenes bioquímicos del historial (si existe)
        $data['examenes_bioquimicos'] = [];
        if (!empty($historialExistente) && $historialExistente->id) {
            $examenModel = new \App\Models\HistorialExamenBioquimico();
            $data['examenes_bioquimicos'] = $examenModel->getPorHistorial($historialExistente->id);
        }

        // Cargar tendencia de consumo (tabla normalizada por grupo)
        $data['tendencia_consumo'] = [];
        $data['tendencia_grupos'] = \App\Models\HistorialTendenciaConsumo::getGrupos();
        if (!empty($historialExistente) && $historialExistente->id) {
            $tendenciaModel = new \App\Models\HistorialTendenciaConsumo();
            $data['tendencia_consumo'] = $tendenciaModel->getPorHistorial($historialExistente->id);
        }

        // Cargar tags sugeridos y tags existentes desde detalle_agenda
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        $data['tags_sugeridos'] = $historialModel->getTagsMasUsados($empresaId, 20);
        
        // Cargar tags desde detalle_agenda (no desde historial_clinico)
        if (!empty($cita->tags)) {
            $tagsArray = json_decode($cita->tags, true);
            if (is_array($tagsArray) && !empty($tagsArray)) {
                $data['tags_string'] = implode(', ', $tagsArray);
            } else {
                $data['tags_string'] = '';
            }
        } else {
            $data['tags_string'] = '';
        }

        // Buscar la última consulta completada del mismo paciente (para mostrar como referencia)
        $consultaAnterior = $db->table('detalle_agenda da')
            ->select('da.*, a.fecha as fecha_agenda,
                      hc.peso_actual as peso_anterior, hc.altura_actual as altura_anterior, 
                      hc.imc_actual as imc_anterior, hc.circunferencia_cintura as cintura_anterior,
                      hc.circunferencia_cadera as cadera_anterior, hc.grasa_corporal as grasa_anterior,
                      hc.masa_muscular as masa_muscular_anterior')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('historial_clinico hc', 'hc.detalle_agenda_id = da.id', 'left')
            ->where('da.paciente_id', $cita->paciente_id)
            ->where('da.usuario_id', $usuario_id)
            ->where('da.estado_cita', 'completada')
            ->where('da.id !=', $detalleAgendaId) // Excluir la consulta actual
            ->where('da.fecha_fin_real IS NOT NULL') // Solo consultas terminadas
            ->orderBy('da.fecha_fin_real', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
        
        $data['consulta_anterior'] = $consultaAnterior;

        // Cargar métodos de cálculo disponibles (igual que en historial/editar)
        $perfilId = $usuario['perfil_id'];
        $rutasPermitidas = $modulo->getAllowedByPerfil($perfilId, $empresaId);
        $metodosDisponibles = [];
        $metodosModel = new MetodoCalculo();
        $todosMetodos = $metodosModel->getMetodosActivos();
        foreach ($todosMetodos as $metodo) {
            $rutaMetodo = '/calcular-' . $metodo->slug;
            $tieneAcceso = false;
            foreach ($rutasPermitidas as $rutaPermitida) {
                if (($rutaPermitida['detalle_ruta'] ?? '') === $rutaMetodo && ($rutaPermitida['permisos']['ver'] ?? false)) {
                    $tieneAcceso = true;
                    break;
                }
            }
            $metodosDisponibles[] = [
                'id' => $metodo->id,
                'nombre' => $metodo->nombre,
                'slug' => $metodo->slug,
                'componentes' => $metodo->componentes,
                'descripcion' => $metodo->descripcion,
                'precio_mensual' => $metodo->precio_mensual,
                'es_addon' => $metodo->es_addon,
                'disponible' => $tieneAcceso
            ];
        }
        $data['metodos_calculo'] = $metodosDisponibles;

        return view('Modulos/agenda/consulta', $data);
    }

    /**
     * Iniciar consulta - Marcar fecha/hora de inicio real
     */
    public function iniciarConsulta()
    {
        $this->response->setContentType('application/json');
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $detalleAgendaId = $this->request->getPost('detalle_agenda_id');
        
        if (!$detalleAgendaId) {
            return $this->response->setJSON([
                'error' => 'Error de validación',
                'message' => 'ID de detalle agenda es requerido'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Verificar que el detalle_agenda pertenece al usuario y tiene paciente
        $detalle = $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->where('usuario_id', $usuario_id)
            ->where('paciente_id IS NOT NULL')
            ->get()
            ->getRow();

        if (!$detalle) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'message' => 'No tiene permiso para iniciar esta consulta'
            ])->setStatusCode(403);
        }

        // Verificar que no esté ya iniciada
        if ($detalle->fecha_inicio_real) {
            return $this->response->setJSON([
                'error' => 'Consulta ya iniciada',
                'message' => 'Esta consulta ya fue iniciada anteriormente'
            ])->setStatusCode(400);
        }

        // Verificar que la cita esté confirmada antes de iniciar
        if ($detalle->estado_cita !== 'confirmada') {
            return $this->response->setJSON([
                'error' => 'Cita no confirmada',
                'message' => 'Solo se pueden iniciar consultas que estén confirmadas. Estado actual: ' . ($detalle->estado_cita ?? 'desconocido')
            ])->setStatusCode(400);
        }

        // Actualizar estado de "confirmada" a "en_proceso" y marcar fecha de inicio
        $fechaInicio = date('Y-m-d H:i:s');
        $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->update([
                'fecha_inicio_real' => $fechaInicio,
                'estado_cita' => 'en_proceso'
            ]);

        $response = $this->response->setJSON([
            'success' => true,
            'message' => 'Consulta iniciada',
            'fecha_inicio' => $fechaInicio,
            'csrf_token' => csrf_hash()
        ]);
        $response->setHeader('X-CSRF-TOKEN', csrf_hash());
        return $response;
    }

    /**
     * Obtener consulta activa del nutricionista (para cronómetro global)
     */
    public function getConsultaActiva()
    {
        $this->response->setContentType('application/json');
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['activa' => false])->setStatusCode(401);
        }

        $usuario_id = session()->get('usuario')['id'];
        $db = \Config\Database::connect();

        // Buscar consulta activa (iniciada pero no terminada)
        $consulta = $db->table('detalle_agenda da')
            ->select('da.id, da.fecha_inicio_real, da.paciente_id, p.nombre, p.apellido, a.fecha as fecha_agenda')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->where('da.usuario_id', $usuario_id)
            ->where('da.fecha_inicio_real IS NOT NULL')
            ->where('da.fecha_fin_real IS NULL')
            ->where('da.estado_cita', 'en_proceso')
            ->orderBy('da.fecha_inicio_real', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

        if ($consulta) {
            return $this->response->setJSON([
                'activa' => true,
                'detalle_agenda_id' => $consulta->id,
                'fecha_inicio' => $consulta->fecha_inicio_real,
                'paciente' => [
                    'id' => $consulta->paciente_id,
                    'nombre' => trim(($consulta->nombre ?? '') . ' ' . ($consulta->apellido ?? ''))
                ],
                'url_consulta' => base_url('dashboard/agenda/consulta?id=' . $consulta->id)
            ]);
        }

        return $this->response->setJSON(['activa' => false]);
    }

    /**
     * Terminar consulta - Marcar fecha/hora de fin real y calcular duración
     */
    public function terminarConsulta()
    {
        $this->response->setContentType('application/json');
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $detalleAgendaId = $this->request->getPost('detalle_agenda_id');
        
        if (!$detalleAgendaId) {
            return $this->response->setJSON([
                'error' => 'Error de validación',
                'message' => 'ID de detalle agenda es requerido'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Verificar que el detalle_agenda pertenece al usuario
        $detalle = $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->where('usuario_id', $usuario_id)
            ->get()
            ->getRow();

        if (!$detalle) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'message' => 'No tiene permiso para terminar esta consulta'
            ])->setStatusCode(403);
        }

        // Verificar que esté iniciada
        if (!$detalle->fecha_inicio_real) {
            return $this->response->setJSON([
                'error' => 'Consulta no iniciada',
                'message' => 'Debe iniciar la consulta antes de terminarla'
            ])->setStatusCode(400);
        }

        // Verificar que no esté ya terminada
        if ($detalle->fecha_fin_real) {
            return $this->response->setJSON([
                'error' => 'Consulta ya terminada',
                'message' => 'Esta consulta ya fue terminada anteriormente'
            ])->setStatusCode(400);
        }

        // Calcular duración
        $fechaInicio = new \DateTime($detalle->fecha_inicio_real);
        $fechaFin = new \DateTime();
        $duracion = $fechaInicio->diff($fechaFin)->i + ($fechaInicio->diff($fechaFin)->h * 60);

        // Actualizar estado y fecha de fin
        $fechaFinStr = $fechaFin->format('Y-m-d H:i:s');
        $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->update([
                'fecha_fin_real' => $fechaFinStr,
                'duracion_real' => $duracion,
                'estado_cita' => 'completada'
            ]);

        $response = $this->response->setJSON([
            'success' => true,
            'message' => 'Consulta finalizada',
            'fecha_fin' => $fechaFinStr,
            'duracion_minutos' => $duracion,
            'csrf_token' => csrf_hash()
        ]);
        $response->setHeader('X-CSRF-TOKEN', csrf_hash());
        return $response;
    }

    /**
     * Guardar notas de la consulta (durante o después)
     */
    public function guardarNotasConsulta()
    {
        $this->response->setContentType('application/json');
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $detalleAgendaId = $this->request->getPost('detalle_agenda_id');
        $notasConsulta = $this->request->getPost('notas_consulta');
        $objetivos = $this->request->getPost('objetivos');
        $planAlimentacion = $this->request->getPost('plan_alimentacion');
        $recomendaciones = $this->request->getPost('recomendaciones');
        $proximaCitaRecomendada = $this->request->getPost('proxima_cita_recomendada');
        $tags = $this->request->getPost('tags');

        if (!$detalleAgendaId) {
            return $this->response->setJSON([
                'error' => 'Error de validación',
                'message' => 'ID de detalle agenda es requerido'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Verificar que el detalle_agenda pertenece al usuario
        $detalle = $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->where('usuario_id', $usuario_id)
            ->get()
            ->getRow();

        if (!$detalle) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'message' => 'No tiene permiso para modificar esta consulta'
            ])->setStatusCode(403);
        }

        // Procesar tags si vienen
        $tagsJson = null;
        if (!empty($tags)) {
            $historialModel = new HistorialClinico();
            $usuario = session()->get('usuario');
            $empresaId = $usuario['empresa_id'] ?? null;
            
            // Limpiar y normalizar tags antes de procesarlos
            $tagsInput = $tags;
            
            // Si los tags vienen como array JSON stringificado, intentar decodificarlos
            if (is_string($tagsInput) && !empty($tagsInput)) {
                // Intentar decodificar si es JSON
                $decoded = json_decode($tagsInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // Si es un array, extraer los valores
                    $tagsArray = [];
                    foreach ($decoded as $item) {
                        if (is_string($item)) {
                            $tagsArray[] = $item;
                        } elseif (is_array($item) && isset($item['value'])) {
                            $tagsArray[] = $item['value'];
                        } elseif (is_array($item) && isset($item['tag'])) {
                            $tagsArray[] = $item['tag'];
                        }
                    }
                    $tagsInput = implode(',', $tagsArray);
                }
            }
            
            $tagsJson = $historialModel->procesarTags($tagsInput, $empresaId);
        }
        
        // Actualizar notas y datos de la consulta
        $dataUpdate = [
            'notas_consulta' => $notasConsulta ?: null,
            'objetivos' => $objetivos ?: null,
            'plan_alimentacion' => $planAlimentacion ?: null,
            'recomendaciones' => $recomendaciones ?: null,
        ];
        
        // Agregar tags si se procesaron
        if ($tagsJson !== null) {
            $dataUpdate['tags'] = $tagsJson;
        }

        // Próxima cita recomendada: puede ser solo fecha (DD-MM-YYYY) o fecha + hora (DD-MM-YYYY HH:mm - HH:mm)
        $proximaCitaRecomendada = $proximaCitaRecomendada !== null ? trim($proximaCitaRecomendada) : '';
        if ($proximaCitaRecomendada !== '') {
            $tieneHora = (strpos($proximaCitaRecomendada, ' - ') !== false);
            $valorParaGuardar = null;
            if (preg_match('/^(\d{2})-(\d{2})-(\d{4})(?:\s|$)/', $proximaCitaRecomendada, $matches)) {
                $soloFechaYyyyMmDd = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                if ($tieneHora) {
                    $tipoColumna = $db->query("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'detalle_agenda' AND COLUMN_NAME = 'proxima_cita_recomendada'", [$db->getDatabase()])->getRow();
                    $esVarchar = $tipoColumna && in_array(strtolower($tipoColumna->DATA_TYPE ?? ''), ['varchar', 'char', 'text'], true);
                    $valorParaGuardar = $esVarchar ? $proximaCitaRecomendada : $soloFechaYyyyMmDd;
                } else {
                    $valorParaGuardar = $soloFechaYyyyMmDd;
                }
            } else {
                $valorParaGuardar = $proximaCitaRecomendada;
            }
            $dataUpdate['proxima_cita_recomendada'] = $valorParaGuardar;
        } else {
            $dataUpdate['proxima_cita_recomendada'] = null;
        }

        $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->update($dataUpdate);

        $response = $this->response->setJSON([
            'success' => true,
            'message' => 'Información de la consulta guardada correctamente',
            'csrf_token' => csrf_hash()
        ]);
        $response->setHeader('X-CSRF-TOKEN', csrf_hash());
        return $response;
    }

    /**
     * Obtener consultas próximas (para notificaciones)
     */
    public function getConsultasProximas()
    {
        $this->response->setContentType('application/json');
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];
        $minutosAnticipacion = $this->request->getGet('minutos') ?: 15; // Por defecto 15 minutos

        // Obtener consultas confirmadas o agendadas que empiezan en los próximos X minutos
        $ahora = new \DateTime();
        $limite = clone $ahora;
        $limite->modify("+{$minutosAnticipacion} minutes");

        $consultas = $db->table('detalle_agenda da')
            ->select('da.id, da.hora_inicio, a.fecha, p.nombre, p.apellido, ma.nombre as modalidad')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
            ->where('da.usuario_id', $usuario_id)
            ->where('da.paciente_id IS NOT NULL')
            ->whereIn('da.estado_cita', ['pendiente', 'confirmada'])
            ->where('da.fecha_inicio_real IS NULL') // No iniciadas aún
            ->get()
            ->getResult();

        $proximas = [];
        foreach ($consultas as $cita) {
            // Convertir fecha y hora a DateTime
            $fecha = $cita->fecha;
            if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fecha, $matches)) {
                $fecha = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            $fechaHora = new \DateTime($fecha . ' ' . $cita->hora_inicio);
            
            // Si está dentro del rango de notificación
            if ($fechaHora >= $ahora && $fechaHora <= $limite) {
                $proximas[] = [
                    'id' => $cita->id,
                    'fecha' => $cita->fecha,
                    'hora' => date('H:i', strtotime($cita->hora_inicio)),
                    'paciente' => trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? '')),
                    'modalidad' => $cita->modalidad ?? 'No definida',
                    'minutos_restantes' => round(($fechaHora->getTimestamp() - $ahora->getTimestamp()) / 60)
                ];
            }
        }

        return $this->response->setJSON($proximas);
    }

    /**
     * Guardar o actualizar mediciones corporales en historial clínico
     */
    public function guardarMediciones()
    {
        $this->response->setContentType('application/json');
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $post = $this->request->getPost();
        $detalleAgendaId = $post['detalle_agenda_id'] ?? null;
        $pacienteId = $post['paciente_id'] ?? null;
        $historialId = $post['historial_id'] ?? null;

        if (!$detalleAgendaId || !$pacienteId) {
            return $this->response->setJSON([
                'error' => 'Datos incompletos',
                'message' => 'Faltan datos requeridos (detalle_agenda_id, paciente_id)'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Verificar que el detalle_agenda pertenece al usuario y tiene el paciente correcto
        $detalle = $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->where('usuario_id', $usuario_id)
            ->where('paciente_id', $pacienteId)
            ->get()
            ->getRow();

        if (!$detalle) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'message' => 'No tiene permiso para modificar esta consulta'
            ])->setStatusCode(403);
        }

        // Obtener fecha y hora de la cita
        $agenda = $db->table('agenda')
            ->where('id', $detalle->agenda_id)
            ->get()
            ->getRow();

        $historialModel = new HistorialClinico();

        // Calcular IMC si hay peso y altura
        $imc_actual = null;
        if (!empty($post['peso_actual']) && !empty($post['altura_actual'])) {
            $imc_actual = $historialModel->calcularIMC($post['peso_actual'], $post['altura_actual']);
        }

        // Calcular suma de pliegues
        $suma_pliegues = null;
        $pliegues = [
            'pliegue_tricipital', 'pliegue_bicipital', 'pliegue_subescapular',
            'pliegue_suprailíaco', 'pliegue_abdominal', 'pliegue_muslo_anterior',
            'pliegue_pantorrilla_medial', 'pliegue_pectoral', 'pliegue_axilar_medio',
            'pliegue_muslo_medial'
        ];
        $suma = 0;
        $tiene_pliegues = false;
        foreach ($pliegues as $pliegue) {
            if (!empty($post[$pliegue])) {
                $suma += floatval($post[$pliegue]);
                $tiene_pliegues = true;
            }
        }
        if ($tiene_pliegues) {
            $suma_pliegues = round($suma, 2);
        }

        // Calcular grasa corporal a partir de pliegues (fórmula simplificada)
        // Nota: Se puede mejorar con fórmulas específicas por género y edad
        $grasa_corporal_calculada = null;
        if ($suma_pliegues && !empty($post['peso_actual']) && !empty($post['altura_actual'])) {
            // Fórmula de Durnin-Womersley simplificada (requiere edad, pero usamos una aproximación)
            // Por ahora, una fórmula básica basada en suma de pliegues
            // Esto se puede mejorar con fórmulas más específicas
            $grasa_corporal_calculada = round(($suma_pliegues * 0.5) + 5, 2); // Fórmula simplificada
        }

        // Obtener fecha en formato DD-MM-YYYY (igual que agenda y detalle_agenda)
        $fechaConsulta = $detalle->fecha ?: ($agenda->fecha ?? null);
        
        // Si no hay fecha, usar la fecha actual en formato DD-MM-YYYY
        if (!$fechaConsulta) {
            $fechaConsulta = date('d-m-Y');
        }
        
        // Asegurar que la fecha esté en formato DD-MM-YYYY
        // Si viene en YYYY-MM-DD, convertir a DD-MM-YYYY
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fechaConsulta, $matches)) {
            $fechaConsulta = $matches[3] . '-' . $matches[2] . '-' . $matches[1]; // Convertir a DD-MM-YYYY
        } elseif (!preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fechaConsulta)) {
            // Si no está en ningún formato reconocido, usar fecha actual
            $fechaConsulta = date('d-m-Y');
        }

        $dataHistorial = [
            'paciente_id' => $pacienteId,
            'nutricionista_id' => $usuario_id,
            'agenda_id' => $detalle->agenda_id,
            'detalle_agenda_id' => $detalleAgendaId,
            'tipo_registro' => $detalle->tipo_consulta ?? 'consulta',
            'fecha_consulta' => $fechaConsulta,
            'hora_consulta' => $detalle->hora_inicio,
            'peso_actual' => !empty($post['peso_actual']) ? $post['peso_actual'] : null,
            'altura_actual' => !empty($post['altura_actual']) ? $post['altura_actual'] : null,
            'altura_sentado' => !empty($post['altura_sentado']) ? $post['altura_sentado'] : null,
            'imc_actual' => $imc_actual,
            'circunferencia_cintura' => !empty($post['circunferencia_cintura']) ? $post['circunferencia_cintura'] : null,
            'circunferencia_cadera' => !empty($post['circunferencia_cadera']) ? $post['circunferencia_cadera'] : null,
            'circunferencia_brazo_relajado' => !empty($post['circunferencia_brazo_relajado']) ? $post['circunferencia_brazo_relajado'] : null,
            'circunferencia_brazo_contraido' => !empty($post['circunferencia_brazo_contraido']) ? $post['circunferencia_brazo_contraido'] : null,
            'circunferencia_muslo_medio' => !empty($post['circunferencia_muslo_medio']) ? $post['circunferencia_muslo_medio'] : null,
            'circunferencia_pantorrilla' => !empty($post['circunferencia_pantorrilla']) ? $post['circunferencia_pantorrilla'] : null,
            'circunferencia_cuello' => !empty($post['circunferencia_cuello']) ? $post['circunferencia_cuello'] : null,
            'circunferencia_torax' => !empty($post['circunferencia_torax']) ? $post['circunferencia_torax'] : null,
            'circunferencia_cabeza' => !empty($post['circunferencia_cabeza']) ? $post['circunferencia_cabeza'] : null,
            'circunferencia_antebrazo_maximo' => !empty($post['circunferencia_antebrazo_maximo']) ? $post['circunferencia_antebrazo_maximo'] : null,
            'circunferencia_muslo_maximo' => !empty($post['circunferencia_muslo_maximo']) ? $post['circunferencia_muslo_maximo'] : null,
            'circunferencia_muneca' => !empty($post['circunferencia_muneca']) ? $post['circunferencia_muneca'] : null,
            'diametro_biacromial' => !empty($post['diametro_biacromial']) ? $post['diametro_biacromial'] : null,
            'diametro_bi_iliocristal' => !empty($post['diametro_bi_iliocristal']) ? $post['diametro_bi_iliocristal'] : null,
            'diametro_torax_transverso' => !empty($post['diametro_torax_transverso']) ? $post['diametro_torax_transverso'] : null,
            'diametro_torax_anteroposterior' => !empty($post['diametro_torax_anteroposterior']) ? $post['diametro_torax_anteroposterior'] : null,
            'diametro_humero' => !empty($post['diametro_humero']) ? $post['diametro_humero'] : null,
            'diametro_femur' => !empty($post['diametro_femur']) ? $post['diametro_femur'] : null,
            'diametro_muneca' => !empty($post['diametro_muneca']) ? $post['diametro_muneca'] : null,
            'diametro_tobillo' => !empty($post['diametro_tobillo']) ? $post['diametro_tobillo'] : null,
            'grasa_corporal' => !empty($post['grasa_corporal']) ? $post['grasa_corporal'] : null,
            'masa_muscular' => !empty($post['masa_muscular']) ? $post['masa_muscular'] : null,
            'pliegue_tricipital' => !empty($post['pliegue_tricipital']) ? $post['pliegue_tricipital'] : null,
            'pliegue_bicipital' => !empty($post['pliegue_bicipital']) ? $post['pliegue_bicipital'] : null,
            'pliegue_subescapular' => !empty($post['pliegue_subescapular']) ? $post['pliegue_subescapular'] : null,
            'pliegue_suprailíaco' => !empty($post['pliegue_suprailíaco']) ? $post['pliegue_suprailíaco'] : null,
            'pliegue_supraespinal' => !empty($post['pliegue_supraespinal']) ? $post['pliegue_supraespinal'] : null,
            'pliegue_abdominal' => !empty($post['pliegue_abdominal']) ? $post['pliegue_abdominal'] : null,
            'pliegue_muslo_anterior' => !empty($post['pliegue_muslo_anterior']) ? $post['pliegue_muslo_anterior'] : null,
            'pliegue_pantorrilla_medial' => !empty($post['pliegue_pantorrilla_medial']) ? $post['pliegue_pantorrilla_medial'] : null,
            'pliegue_pectoral' => !empty($post['pliegue_pectoral']) ? $post['pliegue_pectoral'] : null,
            'pliegue_axilar_medio' => !empty($post['pliegue_axilar_medio']) ? $post['pliegue_axilar_medio'] : null,
            'pliegue_muslo_medial' => !empty($post['pliegue_muslo_medial']) ? $post['pliegue_muslo_medial'] : null,
            'suma_pliegues' => $suma_pliegues,
            'grasa_corporal_calculada' => $grasa_corporal_calculada,
            'anamnesis' => !empty($post['anamnesis']) ? $post['anamnesis'] : null,
            'anamnesis_clinica' => !empty($post['anamnesis_clinica']) ? $post['anamnesis_clinica'] : null,
            'anamnesis_alimentaria' => !empty($post['anamnesis_alimentaria']) ? $post['anamnesis_alimentaria'] : null,
            'tendencia_consumo' => null, // Se guarda en tabla historial_tendencia_consumo
            'recordatorio_24h' => !empty($post['recordatorio_24h']) ? $post['recordatorio_24h'] : null,
            'diagnostico' => !empty($post['diagnostico']) ? $post['diagnostico'] : null,
            'plan_tratamiento' => !empty($post['plan_tratamiento']) ? $post['plan_tratamiento'] : null,
            'estado' => 'A'
        ];

        // Procesar tags y guardarlos en detalle_agenda
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        
        // Limpiar y normalizar tags antes de procesarlos
        $tagsInput = $post['tags'] ?? '';
        
        // Si los tags vienen como array JSON stringificado, intentar decodificarlos
        if (is_string($tagsInput) && !empty($tagsInput)) {
            // Intentar decodificar si es JSON
            $decoded = json_decode($tagsInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // Si es un array, extraer los valores
                $tagsArray = [];
                foreach ($decoded as $item) {
                    if (is_string($item)) {
                        $tagsArray[] = $item;
                    } elseif (is_array($item) && isset($item['value'])) {
                        $tagsArray[] = $item['value'];
                    } elseif (is_array($item) && isset($item['tag'])) {
                        $tagsArray[] = $item['tag'];
                    }
                }
                $tagsInput = implode(',', $tagsArray);
            }
        }
        
        $tagsJson = $historialModel->procesarTags($tagsInput, $empresaId);
        
        // Guardar tags en detalle_agenda
        $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->update(['tags' => $tagsJson]);
        
        // También guardar en historial_clinico para sincronización
        $dataHistorial['tags'] = $tagsJson;

        // Sincronizar datos de detalle_agenda si están disponibles
        if (!empty($detalle->motivo)) {
            $dataHistorial['motivo_consulta'] = $detalle->motivo;
        }
        if (!empty($detalle->notas_consulta)) {
            $dataHistorial['anamnesis'] = $dataHistorial['anamnesis'] ? 
                $dataHistorial['anamnesis'] . "\n\n" . $detalle->notas_consulta : 
                $detalle->notas_consulta;
        }
        if (!empty($detalle->objetivos)) {
            $dataHistorial['diagnostico'] = $dataHistorial['diagnostico'] ? 
                $dataHistorial['diagnostico'] . "\n\nObjetivos: " . $detalle->objetivos : 
                "Objetivos: " . $detalle->objetivos;
        }
        if (!empty($detalle->plan_alimentacion)) {
            $dataHistorial['plan_tratamiento'] = $dataHistorial['plan_tratamiento'] ? 
                $dataHistorial['plan_tratamiento'] . "\n\nPlan Alimentación: " . $detalle->plan_alimentacion : 
                "Plan Alimentación: " . $detalle->plan_alimentacion;
        }
        if (!empty($detalle->recomendaciones)) {
            $dataHistorial['recomendaciones'] = $detalle->recomendaciones;
        }

        try {
            if ($historialId) {
                // Actualizar registro existente
                $historialModel->update($historialId, $dataHistorial);
                $mensaje = 'Mediciones actualizadas correctamente';
            } else {
                // Crear nuevo registro
                $nuevoId = $historialModel->insert($dataHistorial);
                $mensaje = 'Mediciones guardadas correctamente';
                $historialId = $nuevoId;
            }

            // Exámenes bioquímicos: reemplazar todos los del historial
            $examenModel = new \App\Models\HistorialExamenBioquimico();
            $db->table('historial_examen_bioquimico')->where('historial_clinico_id', $historialId)->delete();
            $examenesRaw = $post['examenes_bioquimicos'] ?? '';
            if (is_string($examenesRaw) && $examenesRaw !== '') {
                $examenes = json_decode($examenesRaw, true);
                if (is_array($examenes)) {
                    foreach ($examenes as $row) {
                        if (empty($row['nombre']) && empty($row['valor']) && empty($row['fecha_interpretacion'])) {
                            continue;
                        }
                        $examenModel->insert([
                            'historial_clinico_id' => $historialId,
                            'nombre' => $row['nombre'] ?? null,
                            'valor' => $row['valor'] ?? null,
                            'fecha_interpretacion' => $row['fecha_interpretacion'] ?? null
                        ]);
                    }
                }
            }

            // Tendencia de consumo: reemplazar todos los del historial (tabla normalizada)
            $tendenciaModel = new \App\Models\HistorialTendenciaConsumo();
            $db->table('historial_tendencia_consumo')->where('historial_clinico_id', $historialId)->delete();
            $tendenciaRaw = $post['tendencia_consumo'] ?? '';
            if (is_string($tendenciaRaw) && $tendenciaRaw !== '') {
                $tendenciaRows = json_decode($tendenciaRaw, true);
                if (is_array($tendenciaRows)) {
                    foreach ($tendenciaRows as $row) {
                        $grupo = $row['grupo'] ?? null;
                        if (empty($grupo)) {
                            continue;
                        }
                        $tendenciaModel->insert([
                            'historial_clinico_id' => $historialId,
                            'grupo' => $grupo,
                            'preferencia' => $row['preferencia'] ?? null,
                            'alergia_intolerancia' => $row['alergia_intolerancia'] ?? null
                        ]);
                    }
                }
            }

            $response = $this->response->setJSON([
                'success' => true,
                'message' => $mensaje,
                'historial_id' => $historialId,
                'csrf_token' => csrf_hash()
            ]);
            $response->setHeader('X-CSRF-TOKEN', csrf_hash());
            return $response;

        } catch (\Exception $e) {
            log_message('error', 'Error al guardar mediciones: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Error al guardar',
                'message' => 'Ocurrió un error al guardar las mediciones: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Vista de estadísticas de consultas
     */
    public function estadisticas()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        return view('Modulos/agenda/estadisticas', $data);
    }

    /**
     * Obtener estadísticas de consultas (API)
     */
    public function getEstadisticas()
    {
        $this->response->setContentType('application/json');
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];
        $fechaDesde = $this->request->getGet('fecha_desde');
        $fechaHasta = $this->request->getGet('fecha_hasta');

        $builder = $db->table('detalle_agenda da')
            ->select('da.id, da.estado_cita, da.duracion_real, da.fecha_inicio_real, da.fecha_fin_real,
                      a.fecha, p.nombre, p.apellido, ma.nombre as modalidad')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
            ->where('da.usuario_id', $usuario_id)
            ->where('da.paciente_id IS NOT NULL');

        if ($fechaDesde) {
            $builder->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') >= ", $fechaDesde);
        }
        if ($fechaHasta) {
            $builder->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') <= ", $fechaHasta);
        }

        $consultas = $builder->orderBy('a.fecha', 'DESC')
                           ->orderBy('da.hora_inicio', 'DESC')
                           ->get()
                           ->getResult();

        // Calcular resumen
        $totalConsultas = count($consultas);
        $consultasCompletadas = 0;
        $totalMinutos = 0;
        $duraciones = [];

        $estadosCount = [
            'pendiente' => 0,
            'confirmada' => 0,
            'en_proceso' => 0,
            'completada' => 0,
            'cancelada' => 0,
            'no_asistio' => 0
        ];

        foreach ($consultas as $cita) {
            $estado = $cita->estado_cita ?? 'pendiente';
            $estadosCount[$estado] = ($estadosCount[$estado] ?? 0) + 1;

            if ($cita->duracion_real) {
                $consultasCompletadas++;
                $totalMinutos += $cita->duracion_real;
                $duraciones[] = $cita->duracion_real;
            }
        }

        $duracionPromedio = $consultasCompletadas > 0 ? round($totalMinutos / $consultasCompletadas, 1) : 0;

        // Preparar datos para gráficos
        $graficos = [
            'estados' => [
                'labels' => array_keys(array_filter($estadosCount)),
                'data' => array_values(array_filter($estadosCount))
            ],
            'duracion' => [
                'labels' => array_map(function($c) { return trim(($c->nombre ?? '') . ' ' . ($c->apellido ?? '')); }, array_slice($consultas, 0, 10)),
                'data' => array_map(function($c) { return $c->duracion_real ?? 0; }, array_slice($consultas, 0, 10))
            ]
        ];

        // Preparar datos para tabla
        $tablaData = [];
        foreach ($consultas as $cita) {
            $tablaData[] = [
                'fecha' => $cita->fecha,
                'paciente' => trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? '')),
                'duracion' => $cita->duracion_real ?? 'N/A',
                'estado' => $cita->estado_cita ?? 'pendiente',
                'modalidad' => $cita->modalidad ?? 'No definida',
                'id' => $cita->id
            ];
        }

        return $this->response->setJSON([
            'resumen' => [
                'total_consultas' => $totalConsultas,
                'consultas_completadas' => $consultasCompletadas,
                'total_minutos' => $totalMinutos,
                'duracion_promedio' => $duracionPromedio
            ],
            'graficos' => $graficos,
            'consultas' => $tablaData
        ]);
    }

    /**
     * Iniciar autorización OAuth2 con calendario
     */
    public function conectarCalendario()
    {
        // ============================================
        // LOGS DE DEBUGGING - INICIO DE CONEXIÓN
        // ============================================
        log_message('info', '========================================');
        log_message('info', 'CONECTAR CALENDARIO EJECUTADO');
        log_message('info', '========================================');
        log_message('info', 'Timestamp: ' . date('Y-m-d H:i:s'));
        log_message('info', 'URL actual: ' . current_url());
        
        if (!session()->get('usuario')) {
            log_message('error', 'No hay sesión de usuario');
            return redirect()->to(base_url('login'));
        }

        $usuarioId = session()->get('usuario')['id'];
        log_message('info', 'Usuario ID: ' . $usuarioId);
        
        try {
            // No pasar $usuarioId al constructor para evitar cargar token inexistente/expirado
            // Solo necesitamos generar la URL de autorización
            $calendarService = new CalendarService();
            $authUrl = $calendarService->getAuthUrl($usuarioId);
            
            log_message('info', 'URL de autorización generada: ' . $authUrl);
            log_message('info', 'Redirigiendo a Google...');
            log_message('info', '========================================');
            
            return redirect()->to($authUrl);
        } catch (\Exception $e) {
            log_message('error', 'Error al conectar calendario: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error al conectar con el calendario: ' . $e->getMessage());
        }
    }

    /**
     * Callback de OAuth2 después de autorizar
     */
    public function calendarCallback()
    {
        // ============================================
        // LOGS DE DEBUGGING - INICIO DEL CALLBACK
        // ============================================
        log_message('info', '========================================');
        log_message('info', 'CALLBACK DE CALENDARIO EJECUTADO');
        log_message('info', '========================================');
        log_message('info', 'Timestamp: ' . date('Y-m-d H:i:s'));
        log_message('info', 'IP del cliente: ' . $this->request->getIPAddress());
        log_message('info', 'URL completa: ' . current_url());
        log_message('info', 'Método HTTP: ' . $this->request->getMethod());
        log_message('info', 'GET params: ' . json_encode($this->request->getGet()));
        log_message('info', '========================================');
        
        $code = $this->request->getGet('code');
        $state = $this->request->getGet('state');
        $error = $this->request->getGet('error');
        
        log_message('info', 'Code presente: ' . ($code ? 'SÍ (' . substr($code, 0, 20) . '...)' : 'NO'));
        log_message('info', 'State presente: ' . ($state ? 'SÍ (' . substr($state, 0, 20) . '...)' : 'NO'));
        log_message('info', 'Error presente: ' . ($error ? 'SÍ (' . $error . ')' : 'NO'));
        
        // Si hay un error de Google
        if ($error) {
            log_message('error', 'Error de Google OAuth: ' . $error);
            return redirect()->to(base_url('dashboard/agenda/calendario'))->with('error', 'Error de autorización: ' . $error);
        }
        
        if (!$code || !$state) {
            log_message('error', 'Callback sin código o state. Code: ' . ($code ? 'presente' : 'ausente') . ', State: ' . ($state ? 'presente' : 'ausente'));
            log_message('error', 'IP del cliente: ' . $this->request->getIPAddress());
            return redirect()->to(base_url('dashboard/agenda/calendario'))->with('error', 'Error en la autorización: faltan parámetros');
        }
        
        try {
            // Decodificar y validar state
            $stateDecoded = base64_decode($state, true);
            if ($stateDecoded === false) {
                log_message('error', 'State no es base64 válido. IP: ' . $this->request->getIPAddress());
                throw new \Exception('State inválido: formato incorrecto');
            }
            
            $stateData = json_decode($stateDecoded, true);
            
            if (!$stateData || !is_array($stateData)) {
                log_message('error', 'State no es JSON válido. IP: ' . $this->request->getIPAddress());
                throw new \Exception('State inválido: datos corruptos');
            }
            
            $usuarioId = $stateData['usuario_id'] ?? null;
            $provider = $stateData['provider'] ?? null;
            $timestamp = $stateData['timestamp'] ?? null;
            
            // Validar que el usuario_id sea numérico y válido
            if (!$usuarioId || !is_numeric($usuarioId) || $usuarioId <= 0) {
                log_message('error', 'Usuario ID inválido en state. IP: ' . $this->request->getIPAddress());
                throw new \Exception('Usuario no identificado en el state');
            }
            
            // Validar que el usuario existe en la base de datos
            $db = \Config\Database::connect();
            $usuario = $db->table('usuario')
                ->where('id', $usuarioId)
                ->where('estado', 'A')
                ->get()
                ->getRow();
            
            if (!$usuario) {
                log_message('error', 'Usuario ID ' . $usuarioId . ' no existe o está inactivo. IP: ' . $this->request->getIPAddress());
                throw new \Exception('Usuario no válido');
            }
            
            // Validar timestamp (opcional: el state no debe ser muy antiguo, máximo 10 minutos)
            if ($timestamp && (time() - $timestamp) > 600) {
                log_message('error', 'State expirado. IP: ' . $this->request->getIPAddress());
                throw new \Exception('La autorización expiró. Por favor, intenta de nuevo.');
            }
            
            log_message('info', 'State validado correctamente para usuario ID: ' . $usuarioId);
            log_message('info', 'Procesando callback de calendario para usuario ID: ' . $usuarioId);
            
            // El código de autorización solo es válido una vez y por tiempo limitado
            // Google valida esto, pero aún así es seguro
            // No pasar $usuarioId al constructor para evitar cargar token expirado
            // El token se guardará después de intercambiar el código
            $calendarService = new CalendarService();
            $calendarService->setProvider($provider ?? 'google'); // Establecer el provider desde el state
            $tokenData = $calendarService->exchangeCodeForToken($code, $usuarioId);
            
            log_message('info', 'Tokens guardados exitosamente para usuario ID: ' . $usuarioId);
            
            return redirect()->to(base_url('dashboard/agenda/calendario'))->with('success', 'Calendario conectado exitosamente');
        } catch (\Exception $e) {
            log_message('error', 'Error en callback de calendario: ' . $e->getMessage());
            log_message('error', 'IP del cliente: ' . $this->request->getIPAddress());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->to(base_url('dashboard/agenda/calendario'))->with('error', 'Error al conectar: ' . $e->getMessage());
        }
    }

    /**
     * Verificar y renovar token de calendario automáticamente
     * Este endpoint se puede llamar periódicamente mientras el usuario está activo
     */
    public function verificarTokenCalendario()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay sesión de usuario'
            ]);
        }

        $usuarioId = session()->get('usuario')['id'];
        
        try {
            $calendarService = new CalendarService();
            $resultado = $calendarService->verificarYRenovarTokenAutomatico($usuarioId);
            
            return $this->response->setJSON($resultado);
        } catch (\Exception $e) {
            log_message('error', 'Error al verificar token de calendario: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al verificar token: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Crear evento en el calendario cuando se agenda una cita
     */
    private function crearEventoCalendario($detalleAgendaId, $usuarioId)
    {
        // Verificar si el calendario está configurado
        if (empty(env('CALENDAR_PROVIDER'))) {
            return false;
        }
        
        try {
            $calendarService = new CalendarService($usuarioId);
            
            // Obtener configuración del usuario para saber si agregar paciente como invitado
            $configuracionModel = new \App\Models\EmpresaConfiguracion();
            $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($usuarioId);
            $agregarPacienteComoInvitado = $configuracion['agregar_paciente_como_invitado'] ?? 1;
            
            // Obtener información completa de la cita
            $db = \Config\Database::connect();
            $cita = $db->table('detalle_agenda da')
                ->select('da.*, a.fecha, p.nombre, p.apellido, p.email as email_paciente, 
                         u.nombre as nombre_nutricionista, u.correo as email_nutricionista, ma.nombre as modalidad')
                ->join('agenda a', 'a.id = da.agenda_id', 'left')
                ->join('pacientes p', 'p.id = da.paciente_id', 'left')
                ->join('usuario u', 'u.id = da.usuario_id', 'left')
                ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
                ->where('da.id', $detalleAgendaId)
                ->get()
                ->getRow();
            
            if (!$cita) {
                return false;
            }
            
            $citaData = [
                'detalle_agenda_id' => $detalleAgendaId,
                'usuario_id' => $usuarioId, // Necesario para cargar/refrescar token
                'fecha' => $cita->fecha,
                'hora_inicio' => $cita->hora_inicio,
                'hora_fin' => $cita->hora_fin,
                'nombre_paciente' => trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? '')),
                'email_paciente' => ($agregarPacienteComoInvitado && !empty($cita->email_paciente)) ? $cita->email_paciente : null,
                'nombre_nutricionista' => $cita->nombre_nutricionista ?? 'Nutricionista',
                'email_nutricionista' => $cita->email_nutricionista ?? null, // Email del nutricionista (organizador del evento)
                'tipo_consulta' => ucfirst(str_replace('_', ' ', $cita->tipo_consulta ?? 'control')),
                'modalidad' => $cita->modalidad ?? 'No definida',
                'motivo' => $cita->motivo ?? ''
            ];
            
            $resultado = $calendarService->crearEvento($detalleAgendaId, $citaData);
            
            if ($resultado['success']) {
                log_message('info', 'Evento creado en calendario para cita ID: ' . $detalleAgendaId . 
                    ($agregarPacienteComoInvitado && !empty($cita->email_paciente) ? ' (con paciente como invitado)' : ' (sin paciente como invitado)'));
            }
            
            return $resultado;
        } catch (\Exception $e) {
            log_message('error', 'Error al crear evento en calendario: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar evento en calendario cuando cambia el estado de la cita
     */
    private function actualizarEventoCalendario($detalleAgendaId, $usuarioId)
    {
        if (empty(env('CALENDAR_PROVIDER'))) {
            return false;
        }
        
        try {
            $db = \Config\Database::connect();
            $cita = $db->table('detalle_agenda da')
                ->select('da.*, a.fecha, da.calendar_event_id, p.nombre, p.apellido, 
                         u.nombre as nombre_nutricionista, ma.nombre as modalidad')
                ->join('agenda a', 'a.id = da.agenda_id', 'left')
                ->join('pacientes p', 'p.id = da.paciente_id', 'left')
                ->join('usuario u', 'u.id = da.usuario_id', 'left')
                ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
                ->where('da.id', $detalleAgendaId)
                ->get()
                ->getRow();
            
            if (!$cita || empty($cita->calendar_event_id)) {
                return false; // No hay evento para actualizar
            }
            
            $calendarService = new CalendarService($usuarioId);
            
            $citaData = [
                'fecha' => $cita->fecha,
                'hora_inicio' => $cita->hora_inicio,
                'hora_fin' => $cita->hora_fin,
                'nombre_paciente' => trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? '')),
                'nombre_nutricionista' => $cita->nombre_nutricionista ?? 'Nutricionista',
                'tipo_consulta' => ucfirst(str_replace('_', ' ', $cita->tipo_consulta ?? 'control')),
                'modalidad' => $cita->modalidad ?? 'No definida',
                'estado_cita' => $cita->estado_cita ?? 'pendiente'
            ];
            
            $calendarService->actualizarEvento($cita->calendar_event_id, $citaData);
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar evento en calendario: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar evento del calendario cuando se cancela/elimina una cita
     */
    private function eliminarEventoCalendario($detalleAgendaId, $usuarioId)
    {
        if (empty(env('CALENDAR_PROVIDER'))) {
            return false;
        }
        
        try {
            $db = \Config\Database::connect();
            $cita = $db->table('detalle_agenda')
                ->select('calendar_event_id')
                ->where('id', $detalleAgendaId)
                ->get()
                ->getRow();
            
            if (!$cita || empty($cita->calendar_event_id)) {
                return false;
            }
            
            $calendarService = new CalendarService($usuarioId);
            $calendarService->eliminarEvento($cita->calendar_event_id);
            
            // Limpiar calendar_event_id
            $db->table('detalle_agenda')
                ->where('id', $detalleAgendaId)
                ->update(['calendar_event_id' => null]);
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar evento del calendario: ' . $e->getMessage());
            return false;
        }
    }
}
