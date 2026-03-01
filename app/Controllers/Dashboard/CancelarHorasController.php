<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\EmpresaConfiguracion;
use App\Libraries\WhatsAppService;
use App\Libraries\CalendarService;

class CancelarHorasController extends BaseController
{
    protected $whatsappService;
    protected $calendarService;

    public function __construct()
    {
        $empresaId = session()->get('usuario')['empresa_id'] ?? null;
        $this->whatsappService = new WhatsAppService($empresaId);
        $this->calendarService = new CalendarService();
    }

    /**
     * Mostrar formulario de cancelación masiva
     */
    public function index()
    {
        if (!session()->get('usuario')) {
            return redirect()->to(base_url('login'));
        }

        // Cargar menú
        $menuTotal = array();
        $modulo = new \App\Models\ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        // Cargar configuraciones de mensajes
        $usuarioId = session()->get('usuario')['id'];
        $configuracionModel = new EmpresaConfiguracion();
        $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($usuarioId);

        $data['titulo'] = 'Cancelar Horas Masivamente';
        $data['configuracion'] = $configuracion;
        $data['csrf_token'] = csrf_hash();

        return view('modulos/agenda/cancelar_horas', $data);
    }

    /**
     * Obtener citas en el rango de fechas (AJAX)
     */
    public function obtenerCitas()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $fechaInicio = $this->request->getPost('fecha_inicio');
        $fechaFin = $this->request->getPost('fecha_fin');

        if (!$fechaInicio || !$fechaFin) {
            return $this->response->setJSON(['error' => 'Fechas requeridas'])->setStatusCode(400);
        }

        $usuarioId = session()->get('usuario')['id'];
        $db = \Config\Database::connect();

        // Convertir fechas a formato YYYY-MM-DD para comparación
        $fechaInicioFormato = date('Y-m-d', strtotime(str_replace('/', '-', $fechaInicio)));
        $fechaFinFormato = date('Y-m-d', strtotime(str_replace('/', '-', $fechaFin)));

        // Las fechas vienen en formato DD-MM-YYYY desde el frontend
        // Y se almacenan en formato DD-MM-YYYY en la BD
        // Usamos comparación directa de strings ordenados correctamente
        
        // Obtener todas las citas en el rango (con y sin paciente)
        // Usar consulta SQL directa para manejar correctamente las fechas en formato DD-MM-YYYY
        // IMPORTANTE: Filtrar por da.usuario_id (nutricionista dueño del horario) en lugar de a.usuario_id
        $sql = "SELECT da.id, da.fecha, da.hora_inicio, da.hora_fin, da.estado_cita, da.paciente_id, da.calendar_event_id, 
                     p.nombre as paciente_nombre, p.apellido as paciente_apellido, p.email as paciente_email, p.telefono as paciente_telefono,
                     u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido, da.usuario_id
                FROM detalle_agenda da
                LEFT JOIN agenda a ON a.id = da.agenda_id
                LEFT JOIN pacientes p ON p.id = da.paciente_id
                LEFT JOIN usuario u ON u.id = da.usuario_id
                WHERE da.usuario_id = ?
                  AND STR_TO_DATE(da.fecha, '%d-%m-%Y') >= STR_TO_DATE(?, '%d-%m-%Y')
                  AND STR_TO_DATE(da.fecha, '%d-%m-%Y') <= STR_TO_DATE(?, '%d-%m-%Y')
                  AND da.estado_cita != 'cancelada'
                  AND da.estado_cita != 'completada'
                ORDER BY STR_TO_DATE(da.fecha, '%d-%m-%Y') ASC, da.hora_inicio ASC";
        
        $citas = $db->query($sql, [$usuarioId, $fechaInicio, $fechaFin])->getResultArray();

        // Separar citas con y sin paciente
        $citasConPaciente = [];
        $citasSinPaciente = 0;

        foreach ($citas as $cita) {
            if ($cita['paciente_id']) {
                $citasConPaciente[] = $cita;
            } else {
                $citasSinPaciente++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'citas_con_paciente' => $citasConPaciente,
            'total_con_paciente' => count($citasConPaciente),
            'total_sin_paciente' => $citasSinPaciente,
            'total' => count($citas),
            'csrf_token' => csrf_hash()
        ]);
    }

    /**
     * Procesar cancelación masiva (AJAX)
     */
    public function procesarCancelacion()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $fechaInicio = $this->request->getPost('fecha_inicio');
        $fechaFin = $this->request->getPost('fecha_fin');
        $mensajesPersonalizados = $this->request->getPost('mensajes_personalizados'); // Array de {cita_id: mensaje}

        if (!$fechaInicio || !$fechaFin) {
            return $this->response->setJSON(['error' => 'Fechas requeridas'])->setStatusCode(400);
        }

        $usuarioId = session()->get('usuario')['id'];
        $db = \Config\Database::connect();

        // Las fechas vienen en formato DD-MM-YYYY desde el frontend
        // Y se almacenan en formato DD-MM-YYYY en la BD
        
        // Obtener todas las citas en el rango usando consulta SQL directa
        // IMPORTANTE: Filtrar por da.usuario_id (nutricionista dueño del horario) en lugar de a.usuario_id
        $sql = "SELECT da.id, da.fecha, da.hora_inicio, da.hora_fin, da.estado_cita, da.paciente_id, da.calendar_event_id,
                     p.nombre as paciente_nombre, p.apellido as paciente_apellido, p.email as paciente_email, p.telefono as paciente_telefono,
                     u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido, da.usuario_id
                FROM detalle_agenda da
                LEFT JOIN agenda a ON a.id = da.agenda_id
                LEFT JOIN pacientes p ON p.id = da.paciente_id
                LEFT JOIN usuario u ON u.id = da.usuario_id
                WHERE da.usuario_id = ?
                  AND STR_TO_DATE(da.fecha, '%d-%m-%Y') >= STR_TO_DATE(?, '%d-%m-%Y')
                  AND STR_TO_DATE(da.fecha, '%d-%m-%Y') <= STR_TO_DATE(?, '%d-%m-%Y')
                  AND da.estado_cita != 'cancelada'
                  AND da.estado_cita != 'completada'
                ORDER BY STR_TO_DATE(da.fecha, '%d-%m-%Y') ASC, da.hora_inicio ASC";
        
        $citas = $db->query($sql, [$usuarioId, $fechaInicio, $fechaFin])->getResultArray();

        $canceladas = 0;
        $errores = [];
        $mensajesPersonalizadosArray = json_decode($mensajesPersonalizados ?? '{}', true);

        // Cargar configuraciones de mensajes
        $configuracionModel = new EmpresaConfiguracion();
        $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($usuarioId);

        foreach ($citas as $cita) {
            try {
                // Eliminar evento de Google Calendar si existe
                if (!empty($cita['calendar_event_id']) && !empty($cita['usuario_id'])) {
                    try {
                        $calendarService = new CalendarService($cita['usuario_id']);
                        $calendarService->eliminarEvento($cita['calendar_event_id']);
                        log_message('info', 'Evento de calendario eliminado: ' . $cita['calendar_event_id']);
                    } catch (\Exception $e) {
                        log_message('error', 'Error al eliminar evento de calendario: ' . $e->getMessage());
                    }
                }

                // Actualizar estado de la cita
                $dataUpdate = [
                    'estado_cita' => 'cancelada',
                    'fecha_cancelacion' => date('Y-m-d H:i:s'),
                    'paciente_id' => null, // Liberar horario
                    'estado' => 1, // Disponible
                    'calendar_event_id' => null
                ];

                $db->table('detalle_agenda')
                    ->where('id', $cita['id'])
                    ->update($dataUpdate);

                // Si tiene paciente, enviar notificaciones
                if ($cita['paciente_id']) {
                    // Obtener mensaje según estado
                    $estadoCita = $cita['estado_cita'] ?? 'pendiente';
                    $mensajeBase = $this->obtenerMensajePorEstado($configuracion, $estadoCita);
                    
                    // Agregar mensaje personalizado si existe
                    $mensajePersonalizado = $mensajesPersonalizadosArray[$cita['id']] ?? '';
                    $mensajeFinal = $mensajeBase;
                    if (!empty($mensajePersonalizado)) {
                        $mensajeFinal .= "\n\n" . trim($mensajePersonalizado);
                    }

                    // Reemplazar variables en el mensaje (para email)
                    $mensajeFinal = $this->reemplazarVariablesMensaje($mensajeFinal, $cita);

                    // Motivo corto para plantilla WhatsApp: la API no permite newlines/tabs ni más de 4 espacios seguidos
                    $motivoParaWhatsApp = $this->motivoCortoParaPlantilla(
                        $mensajesPersonalizadosArray[$cita['id']] ?? ''
                    );

                    // Enviar WhatsApp con plantilla cancelacion_cita (igual que en Agenda → Cancelar)
                    if ($cita['paciente_telefono']) {
                        try {
                            $resultado = $this->whatsappService->enviarCancelacionCita(
                                (int) $cita['id'],
                                (int) $cita['paciente_id'],
                                $motivoParaWhatsApp
                            );
                            if (!($resultado['success'] ?? false)) {
                                $errores[] = 'WhatsApp a ' . ($cita['paciente_nombre'] ?? '') . ': ' . ($resultado['error'] ?? 'Error');
                            }
                        } catch (\Exception $e) {
                            log_message('error', 'Error al enviar WhatsApp: ' . $e->getMessage());
                            $errores[] = 'Error al enviar WhatsApp a ' . $cita['paciente_nombre'];
                        }
                    }

                    // Enviar Email (solo motivo opcional; el cuerpo del correo es fijo en la plantilla)
                    if ($cita['paciente_email']) {
                        try {
                            $motivoEmail = $mensajesPersonalizadosArray[$cita['id']] ?? '';
                            $this->enviarEmailCancelacion($cita, $motivoEmail);
                        } catch (\Exception $e) {
                            log_message('error', 'Error al enviar Email: ' . $e->getMessage());
                            $errores[] = 'Error al enviar Email a ' . $cita['paciente_nombre'];
                        }
                    }
                }

                $canceladas++;
            } catch (\Exception $e) {
                log_message('error', 'Error al cancelar cita ' . $cita['id'] . ': ' . $e->getMessage());
                $errores[] = 'Error al cancelar cita del ' . $cita['fecha'];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'canceladas' => $canceladas,
            'errores' => $errores,
            'message' => "Se cancelaron {$canceladas} citas exitosamente" . (count($errores) > 0 ? '. Algunos errores: ' . implode(', ', $errores) : ''),
            'csrf_token' => csrf_hash()
        ]);
    }

    /**
     * Motivo corto para la plantilla WhatsApp cancelacion_cita.
     * La API no permite newlines, tabs ni más de 4 espacios consecutivos.
     * Si no hay mensaje personalizado, se devuelve un espacio (no vacío).
     */
    private function motivoCortoParaPlantilla(string $mensajePersonalizado): string
    {
        $texto = trim($mensajePersonalizado);
        if ($texto === '') {
            return ' ';
        }
        $texto = strip_tags($texto);
        $texto = preg_replace('/[\r\n\t]+/', ' ', $texto);
        $texto = preg_replace('/ {2,}/', ' ', $texto);
        $texto = trim($texto);
        if (strlen($texto) > 500) {
            $texto = substr($texto, 0, 497) . '...';
        }
        return $texto !== '' ? $texto : ' ';
    }

    /**
     * Obtener mensaje base según estado de la cita
     */
    private function obtenerMensajePorEstado($configuracion, $estadoCita)
    {
        // Mapear estados a campos de configuración
        $mapEstados = [
            'pendiente' => 'mensaje_cancelacion_pendiente',
            'confirmada' => 'mensaje_cancelacion_confirmada',
            'en_proceso' => 'mensaje_cancelacion_en_proceso'
        ];

        $campo = $mapEstados[$estadoCita] ?? 'mensaje_cancelacion_pendiente';
        $mensaje = $configuracion[$campo] ?? '';

        // Si no hay mensaje configurado, usar uno por defecto
        if (empty($mensaje)) {
            return "Estimado/a [NOMBRE_PACIENTE],\n\nLamentamos informarle que su cita programada para el [FECHA] a las [HORA] ha sido cancelada.\n\nPor favor, contáctenos para reagendar su consulta.\n\nSaludos,\n[NOMBRE_NUTRICIONISTA]";
        }

        return $mensaje;
    }

    /**
     * Reemplazar variables en el mensaje
     */
    private function reemplazarVariablesMensaje($mensaje, $cita)
    {
        $fechaFormateada = date('d/m/Y', strtotime(str_replace('/', '-', $cita['fecha'])));
        $horaFormateada = date('H:i', strtotime($cita['hora_inicio']));

        $variables = [
            '[NOMBRE_PACIENTE]' => ($cita['paciente_nombre'] ?? '') . ' ' . ($cita['paciente_apellido'] ?? ''),
            '[FECHA]' => $fechaFormateada,
            '[HORA]' => $horaFormateada,
            '[NOMBRE_NUTRICIONISTA]' => ($cita['nutricionista_nombre'] ?? '') . ' ' . ($cita['nutricionista_apellido'] ?? '')
        ];

        foreach ($variables as $variable => $valor) {
            $mensaje = str_replace($variable, $valor, $mensaje);
        }

        return $mensaje;
    }

    /**
     * Enviar Email de cancelación
     */
    private function enviarEmailCancelacion($cita, $mensajePersonalizado = '')
    {
        $email = \Config\Services::email();

        $email->setTo($cita['paciente_email']);
        $email->setSubject('Cancelación de Cita - ' . ($cita['nutricionista_nombre'] ?? 'Nutricionista'));

        // Motivo opcional: solo texto corto, sin HTML ni saltos de línea crudos
        $motivo = is_string($mensajePersonalizado) ? trim(strip_tags($mensajePersonalizado)) : '';

        $email->setMessage(view('emails/cancelacion_cita_paciente', [
            'paciente_nombre' => trim(($cita['paciente_nombre'] ?? '') . ' ' . ($cita['paciente_apellido'] ?? '')),
            'fecha' => date('d/m/Y', strtotime(str_replace('/', '-', $cita['fecha']))),
            'hora' => date('H:i', strtotime($cita['hora_inicio'])),
            'motivo' => $motivo,
            'nutricionista_nombre' => trim(($cita['nutricionista_nombre'] ?? '') . ' ' . ($cita['nutricionista_apellido'] ?? ''))
        ]));

        $email->send();
    }
}
