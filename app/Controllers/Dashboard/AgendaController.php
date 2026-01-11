<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\AgendaPaciente;
use App\Models\Paciente;
use App\Models\HistorialClinico;
use App\Models\ModuloDetalle;
use App\Traits\MaintainsFilters;
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
            // Mostrar tanto disponibles (estado = 1) como ocupados (estado = 2)

        $eventos = $builder->get()->getResult();

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
            // Si tiene paciente, usar el estado_cita de la BD (pendiente, confirmada, en_proceso, completada, etc.)
            // Si no tiene paciente, está disponible
            $estadoCita = $estaDisponible ? 'disponible' : ($evento->estado_cita ?? 'pendiente');
            
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
            if ($estaDisponible) {
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

        return $this->response->setJSON($data);
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
            'estado_cita' => $cita->estado_cita ?? null,
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
            'detalle_agenda_id', 'paciente_id', 'tipo_consulta', 'motivo', 'observaciones'
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
            // Estado inicial: "pendiente" (esperando confirmación del paciente)
            $dataUpdate = [
                'paciente_id' => $pacienteId,
                'tipo_consulta' => $post['tipo_consulta'] ?? 'control',
                'motivo' => $post['motivo'] ?? null,
                'observaciones' => $post['observaciones'] ?? null,
                'estado' => 2 // Ocupado
            ];
            
            // Intentar establecer estado_cita como 'pendiente'
            // Si el ENUM no lo permite, usar 'agendada' como fallback
            try {
                $dataUpdate['estado_cita'] = 'pendiente';
            } catch (\Exception $e) {
                // Si falla, usar 'agendada' como valor por defecto
                log_message('warning', 'No se pudo establecer estado_cita como pendiente: ' . $e->getMessage());
                $dataUpdate['estado_cita'] = 'agendada';
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
                // Enviar email de confirmación al paciente
                try {
                    $this->enviarEmailConfirmacion($detalleAgendaId, $pacienteId);
                } catch (\Exception $e) {
                    // No fallar el agendamiento si el email falla, solo loguear
                    log_message('error', 'Error al enviar email de confirmación: ' . $e->getMessage());
                }

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
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $id = $this->request->getPost('id');
        
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

        // Verificar que la cita esté en estado pendiente antes de confirmar
        if ($detalle->estado_cita !== 'pendiente' && $detalle->estado_cita !== 'agendada') {
            return $this->response->setJSON([
                'error' => 'Estado inválido',
                'message' => 'Solo se pueden confirmar citas que estén en estado pendiente. Estado actual: ' . ($detalle->estado_cita ?? 'desconocido')
            ])->setStatusCode(400);
        }

        // Actualizar estado de "pendiente" o "agendada" a "confirmada"
        $updated = $db->table('detalle_agenda')
            ->where('id', $id)
            ->update([
                'estado_cita' => 'confirmada',
                'fecha_confirmacion' => date('Y-m-d H:i:s')
            ]);

        if ($updated) {
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

    public function getAgenda()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $draw = intval($this->request->getGet("draw"));
        
        $fecha_desde = $this->request->getGet('fecha_desde');
        $fecha_hasta = $this->request->getGet('fecha_hasta');
        $estado_cita = $this->request->getGet('estado_cita');
        
        // Obtener citas desde detalle_agenda (donde paciente_id no es NULL)
        $builder = $db->table('detalle_agenda da')
            ->select('da.id, da.paciente_id, da.tipo_consulta, da.motivo, da.estado_cita, da.fecha_confirmacion, da.fecha_cancelacion, da.motivo_cancelacion, da.observaciones, a.fecha, da.hora_inicio, da.hora_fin, p.nombre, p.apellido, p.telefono, p.email')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->where('da.paciente_id IS NOT NULL'); // Solo citas agendadas

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

            $estadoBadge = match ($r->estado_cita) {
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
                                'forma_asignacion' => 'Manual'
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
        $cita = $db->table('detalle_agenda da')
            ->select('da.id, da.hora_inicio, da.hora_fin, da.modalidad_id, da.tipo_consulta, da.motivo,
                      a.fecha, a.usuario_id,
                      p.nombre, p.apellido, p.email as paciente_email,
                      u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido, u.correo as nutricionista_email,
                      ma.nombre as modalidad_nombre')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('usuario u', 'u.id = a.usuario_id', 'left')
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
     * Confirmar cita desde el email (público, sin autenticación)
     */
    public function confirmarDesdeEmail()
    {
        $token = $this->request->getGet('token');
        
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

            // Verificar que la cita no esté ya confirmada
            if ($cita->estado_cita === 'confirmada') {
                return view('emails/respuesta_cita', [
                    'exito' => true,
                    'mensaje' => 'La cita ya estaba confirmada previamente.'
                ]);
            }
            
            // Verificar que la cita esté en estado pendiente (esperando confirmación)
            if ($cita->estado_cita !== 'pendiente') {
                return view('emails/respuesta_cita', [
                    'exito' => false,
                    'mensaje' => 'Esta cita no puede ser confirmada porque su estado actual es: ' . ($cita->estado_cita ?? 'desconocido') . '.'
                ]);
            }

            // Actualizar estado de "pendiente" a "confirmada" cuando el paciente acepta
            $db->table('detalle_agenda')
                ->where('id', $detalleAgendaId)
                ->update([
                    'estado_cita' => 'confirmada',
                    'fecha_confirmacion' => date('Y-m-d H:i:s')
                ]);

            return view('emails/respuesta_cita', [
                'exito' => true,
                'mensaje' => '¡Cita confirmada exitosamente! Te esperamos en la fecha y hora acordada.'
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
        $token = $this->request->getGet('token');
        
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
        $hashEsperado = hash('sha256', $detalleAgendaId . $pacienteId . 'cancelar');
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

            // Verificar que la cita no esté ya cancelada
            if ($cita->estado_cita === 'cancelada') {
                return view('emails/respuesta_cita', [
                    'exito' => true,
                    'mensaje' => 'La cita ya estaba cancelada previamente.'
                ]);
            }

            // Cancelar la cita y liberar el horario
            $db->table('detalle_agenda')
                ->where('id', $detalleAgendaId)
                ->update([
                    'estado_cita' => 'cancelada',
                    'fecha_cancelacion' => date('Y-m-d H:i:s'),
                    'paciente_id' => null,
                    'estado' => 1 // Disponible nuevamente
                ]);

            return view('emails/respuesta_cita', [
                'exito' => true,
                'mensaje' => 'Cita cancelada exitosamente. El horario ha sido liberado.'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al cancelar cita desde email: ' . $e->getMessage());
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

        // Buscar si ya existe un registro de historial clínico para esta cita
        $historialModel = new HistorialClinico();
        $historialExistente = $historialModel->where('detalle_agenda_id', $detalleAgendaId)
            ->where('paciente_id', $cita->paciente_id)
            ->first();
        
        $data['historial'] = $historialExistente;

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

        // Actualizar notas y datos de la consulta
        $dataUpdate = [
            'notas_consulta' => $notasConsulta ?: null,
            'objetivos' => $objetivos ?: null,
            'plan_alimentacion' => $planAlimentacion ?: null,
            'recomendaciones' => $recomendaciones ?: null,
        ];

        // Convertir fecha de próxima cita si viene en DD-MM-YYYY
        if ($proximaCitaRecomendada) {
            if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $proximaCitaRecomendada, $matches)) {
                $dataUpdate['proxima_cita_recomendada'] = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            } else {
                $dataUpdate['proxima_cita_recomendada'] = $proximaCitaRecomendada;
            }
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
            'pliegue_pantorrilla_medial'
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
            'imc_actual' => $imc_actual,
            'circunferencia_cintura' => !empty($post['circunferencia_cintura']) ? $post['circunferencia_cintura'] : null,
            'circunferencia_cadera' => !empty($post['circunferencia_cadera']) ? $post['circunferencia_cadera'] : null,
            'grasa_corporal' => !empty($post['grasa_corporal']) ? $post['grasa_corporal'] : null,
            'masa_muscular' => !empty($post['masa_muscular']) ? $post['masa_muscular'] : null,
            'pliegue_tricipital' => !empty($post['pliegue_tricipital']) ? $post['pliegue_tricipital'] : null,
            'pliegue_bicipital' => !empty($post['pliegue_bicipital']) ? $post['pliegue_bicipital'] : null,
            'pliegue_subescapular' => !empty($post['pliegue_subescapular']) ? $post['pliegue_subescapular'] : null,
            'pliegue_suprailíaco' => !empty($post['pliegue_suprailíaco']) ? $post['pliegue_suprailíaco'] : null,
            'pliegue_abdominal' => !empty($post['pliegue_abdominal']) ? $post['pliegue_abdominal'] : null,
            'pliegue_muslo_anterior' => !empty($post['pliegue_muslo_anterior']) ? $post['pliegue_muslo_anterior'] : null,
            'pliegue_pantorrilla_medial' => !empty($post['pliegue_pantorrilla_medial']) ? $post['pliegue_pantorrilla_medial'] : null,
            'suma_pliegues' => $suma_pliegues,
            'grasa_corporal_calculada' => $grasa_corporal_calculada,
            'anamnesis' => !empty($post['anamnesis']) ? $post['anamnesis'] : null,
            'diagnostico' => !empty($post['diagnostico']) ? $post['diagnostico'] : null,
            'plan_tratamiento' => !empty($post['plan_tratamiento']) ? $post['plan_tratamiento'] : null,
            'estado' => 'A'
        ];

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
}
