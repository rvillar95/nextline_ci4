<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\AgendaPaciente;
use App\Models\Paciente;
use App\Models\ModuloDetalle;
use App\Traits\MaintainsFilters;

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
        
        // Obtener eventos desde detalle_agenda y agenda_paciente
        $builder = $db->table('detalle_agenda da')
            ->select('da.id, a.fecha, da.hora_inicio, da.hora_fin, da.modalidad_id, ap.paciente_id, ap.estado_cita, ap.tipo_consulta, ap.motivo, p.nombre, p.apellido')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('agenda_paciente ap', 'ap.detalle_agenda_id = da.id', 'left')
            ->join('pacientes p', 'p.id = ap.paciente_id', 'left')
            // Convertir fechas para comparación (start y end vienen en YYYY-MM-DD, fecha está en DD-MM-YYYY)
            ->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') >= ", $start)
            ->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') <= ", $end)
            ->where('da.usuario_id', $nutricionista_id)
            ->where('da.estado', 1); // Solo horarios disponibles (estado = 1)

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
            $estadoCita = $estaDisponible ? 'disponible' : ($evento->estado_cita ?? 'agendada');
            
            // Paleta de colores profesional para salud (COLOR = ESTADO únicamente)
            // Diseñada para ser accesible, transmitir calma y profesionalismo
            $color = match ($estadoCita) {
                'disponible' => '#6BCB77',      // Verde suave - estado positivo, saludable
                'confirmada' => '#4A90E2',      // Azul confiable - estado seguro
                'agendada' => '#4A90E2',        // Azul (mismo que confirmada)
                'en_proceso' => '#FFA726',       // Naranjo cálido - estado intermedio
                'pendiente' => '#FFA726',        // Naranjo (mismo que en proceso)
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

            $data[] = [
                'id' => $evento->id,
                'title' => $titulo,
                'start' => $fechaInicio,
                'end' => $fechaFin,
                'color' => $color,
                'textColor' => '#FFFFFF', // Texto blanco para mejor contraste
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

        $agendaPaciente = new AgendaPaciente();
        
        $data = [
            'detalle_agenda_id' => $post['detalle_agenda_id'],
            'paciente_id' => $post['paciente_id'],
            'nutricionista_id' => session()->get('usuario')['id'],
            'tipo_consulta' => $post['tipo_consulta'] ?? 'control',
            'motivo' => $post['motivo'] ?? null,
            'observaciones' => $post['observaciones'] ?? null,
            'estado_cita' => 'agendada'
        ];

        try {
            if ($agendaPaciente->agendarCita($post['detalle_agenda_id'], $post['paciente_id'], $data)) {
                $response = $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Cita agendada con éxito',
                    'csrf_token' => csrf_hash()
                ]);
                $response->setHeader('X-CSRF-TOKEN', csrf_hash());
                return $response;
            } else {
                $errors = $agendaPaciente->errors();
                $errorMsg = !empty($errors) ? implode(', ', array_values($errors)) : 'Error al agendar la cita';
                return $this->response->setJSON([
                    'error' => 'Error al agendar la cita',
                    'message' => $errorMsg
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al agendar cita: ' . $e->getMessage());
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

        $agendaPaciente = new AgendaPaciente();
        
        if ($agendaPaciente->confirmarCita($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cita confirmada']);
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

        $agendaPaciente = new AgendaPaciente();
        
        if ($agendaPaciente->cancelarCita($id, $motivo)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cita cancelada']);
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
        
        $builder = $db->table('agenda_paciente ap')
            ->select('ap.*, a.fecha, da.hora_inicio, da.hora_fin, p.nombre, p.apellido, p.telefono, p.email')
            ->join('detalle_agenda da', 'da.id = ap.detalle_agenda_id', 'left')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = ap.paciente_id', 'left');

        if ($fecha_desde) {
            // Convertir fecha para comparación (fecha en BD está en DD-MM-YYYY)
            $builder->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') >= ", $fecha_desde);
        }
        if ($fecha_hasta) {
            // Convertir fecha para comparación (fecha en BD está en DD-MM-YYYY)
            $builder->where("STR_TO_DATE(a.fecha, '%d-%m-%Y') <= ", $fecha_hasta);
        }
        if ($estado_cita) {
            $builder->where('ap.estado_cita', $estado_cita);
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
                      COUNT(DISTINCT CASE WHEN da.estado = 1 AND NOT EXISTS (SELECT 1 FROM agenda_paciente ap WHERE ap.detalle_agenda_id = da.id) THEN da.id END) as disponibles,
                      COUNT(DISTINCT CASE WHEN EXISTS (SELECT 1 FROM agenda_paciente ap WHERE ap.detalle_agenda_id = da.id) THEN da.id END) as ocupados,
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
            
            // Solo eliminar los que no tienen citas agendadas
            $builder->where('NOT EXISTS (SELECT 1 FROM agenda_paciente ap WHERE ap.detalle_agenda_id = da.id)', null, false);
            
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
}
