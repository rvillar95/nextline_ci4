<?php

namespace App\Libraries;

use App\Models\WhatsAppMensaje;
use App\Models\Paciente;
use Config\Services;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Http\CurlClient;

/**
 * Servicio para manejar integración con WhatsApp
 * Soporta Twilio y WhatsApp Business API
 */
class WhatsAppService
{
    protected $provider; // 'twilio' o 'whatsapp_business'
    protected $config;
    protected $whatsappModel;
    protected $httpClient;

    public function __construct()
    {
        $this->whatsappModel = new WhatsAppMensaje();
        $this->httpClient = Services::curlrequest();
        
        // Cargar configuración desde .env
        $this->provider = env('WHATSAPP_PROVIDER', 'twilio'); // 'twilio' o 'whatsapp_business'
        
        $this->config = [
            'twilio' => [
                'account_sid' => env('TWILIO_ACCOUNT_SID'),
                'auth_token' => env('TWILIO_AUTH_TOKEN'),
                'from_number' => env('TWILIO_WHATSAPP_FROM'), // formato: whatsapp:+1234567890
                'content_sid_confirmacion' => env('TWILIO_CONTENT_SID_CONFIRMACION'), // SID de plantilla para confirmaciones (opcional)
                'api_url' => 'https://api.twilio.com/2010-04-01/Accounts/' . env('TWILIO_ACCOUNT_SID') . '/Messages.json'
            ],
            'whatsapp_business' => [
                'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
                'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
                'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
                'api_url' => 'https://graph.facebook.com/v18.0/' . env('WHATSAPP_PHONE_NUMBER_ID') . '/messages',
                'verify_token' => env('WHATSAPP_VERIFY_TOKEN', 'nextline_verify_token')
            ]
        ];
    }

    /**
     * Enviar mensaje de texto
     * @param string $numeroDestino Número de destino
     * @param string $mensaje Mensaje de texto o null si se usa plantilla
     * @param int|null $pacienteId ID del paciente
     * @param int|null $agendaId ID de la agenda
     * @param int|null $nutricionistaId ID del nutricionista
     * @param string|null $contentSid SID de plantilla de Twilio (opcional)
     * @param string|null $contentVariables Variables para la plantilla en formato JSON (opcional)
     */
    public function enviarMensaje($numeroDestino, $mensaje = null, $pacienteId = null, $agendaId = null, $nutricionistaId = null, $contentSid = null, $contentVariables = null)
    {
        // Normalizar número (eliminar espacios, guiones, etc.)
        $numeroDestino = $this->normalizarNumero($numeroDestino);
        
        try {
            $mensajeId = null;
            $estado = 'pendiente';
            
            if ($this->provider === 'twilio') {
                $resultado = $this->enviarPorTwilio($numeroDestino, $mensaje, $contentSid, $contentVariables);
                $mensajeId = $resultado['message_id'] ?? null;
                $estado = $resultado['status'] ?? 'queued';
            } elseif ($this->provider === 'whatsapp_business') {
                $resultado = $this->enviarPorWhatsAppBusiness($numeroDestino, $mensaje);
                $mensajeId = $resultado['message_id'] ?? null;
                $estado = $resultado['status'] ?? 'sent';
            }
            
            // Obtener el mensaje real que se envió (puede ser diferente si se usó plantilla)
            $mensajeReal = $resultado['body'] ?? $mensaje ?? 'Mensaje desde plantilla';
            
            // Registrar en base de datos
            $this->whatsappModel->registrarEnvio([
                'paciente_id' => $pacienteId,
                'nutricionista_id' => $nutricionistaId,
                'agenda_id' => $agendaId,
                'tipo_mensaje' => $this->determinarTipoMensaje($mensajeReal),
                'numero_destino' => $numeroDestino,
                'numero_origen' => $this->getNumeroOrigen(),
                'mensaje' => $mensajeReal,
                'mensaje_id_api' => $mensajeId,
                'estado_envio' => $estado,
                'metadata' => json_encode($resultado)
            ]);
            
            return [
                'success' => true,
                'message_id' => $mensajeId,
                'status' => $estado
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error al enviar mensaje WhatsApp: ' . $e->getMessage());
            
            // Registrar error en BD
            $this->whatsappModel->registrarEnvio([
                'paciente_id' => $pacienteId,
                'nutricionista_id' => $nutricionistaId,
                'agenda_id' => $agendaId,
                'tipo_mensaje' => 'otro',
                'numero_destino' => $numeroDestino,
                'numero_origen' => $this->getNumeroOrigen(),
                'mensaje' => $mensaje,
                'estado_envio' => 'error',
                'error_mensaje' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Enviar mensaje usando Twilio SDK oficial
     * @param string $numeroDestino Número de destino
     * @param string|null $mensaje Mensaje de texto (opcional si se usa plantilla)
     * @param string|null $contentSid SID de plantilla de Twilio (opcional)
     * @param string|null $contentVariables Variables para la plantilla en formato JSON (opcional)
     */
    protected function enviarPorTwilio($numeroDestino, $mensaje = null, $contentSid = null, $contentVariables = null)
    {
        $config = $this->config['twilio'];
        
        if (empty($config['account_sid']) || empty($config['auth_token']) || empty($config['from_number'])) {
            throw new \Exception('Configuración de Twilio incompleta');
        }
        
        // Formatear número para Twilio (whatsapp:+56912345678)
        $numeroFormateado = $this->formatearNumeroTwilio($numeroDestino);
        
        // Configurar cliente HTTP de Twilio para manejar certificados SSL
        // En desarrollo local (Windows/WAMP), a veces es necesario deshabilitar verificación SSL
        $isDevelopment = (env('CI_ENVIRONMENT') === 'development' || env('CI_ENVIRONMENT') === '');
        $disableSslVerify = env('TWILIO_DISABLE_SSL_VERIFY', $isDevelopment); // Permitir deshabilitar SSL en desarrollo
        
        $curlOptions = [
            CURLOPT_SSL_VERIFYPEER => !$disableSslVerify, // Verificar SSL solo si está habilitado
            CURLOPT_SSL_VERIFYHOST => $disableSslVerify ? 0 : 2, // Verificar host solo si está habilitado
        ];
        
        // Si la verificación SSL está habilitada, intentar usar certificados del sistema
        if (!$disableSslVerify) {
            // Intentar encontrar el bundle de certificados CA
            $caBundlePaths = [
                __DIR__ . '/../../vendor/twilio/sdk/src/Twilio/cacert.pem', // Bundle incluido en Twilio SDK
                'C:/wamp64/bin/php/php8.1.0/extras/ssl/cacert.pem', // WAMP común
                'C:/xampp/apache/bin/curl-ca-bundle.crt', // XAMPP común
                getcwd() . '/vendor/twilio/sdk/src/Twilio/cacert.pem', // Path relativo
            ];
            
            foreach ($caBundlePaths as $caPath) {
                if (file_exists($caPath)) {
                    $curlOptions[CURLOPT_CAINFO] = $caPath;
                    log_message('info', 'Usando certificado CA de Twilio: ' . $caPath);
                    break;
                }
            }
        } else {
            log_message('warning', 'Verificación SSL deshabilitada para Twilio (solo desarrollo)');
        }
        
        $httpClient = new CurlClient($curlOptions);
        
        // Inicializar cliente de Twilio con el HTTP client configurado
        $twilio = new TwilioClient($config['account_sid'], $config['auth_token'], null, null, $httpClient);
        
        // Preparar parámetros del mensaje
        $params = [
            'from' => $config['from_number'],
            'to' => $numeroFormateado
        ];
        
        // Si se proporciona una plantilla (contentSid), usarla
        if (!empty($contentSid)) {
            $params['contentSid'] = $contentSid;
            
            // Si hay variables para la plantilla, agregarlas
            if (!empty($contentVariables)) {
                // Asegurar que contentVariables sea un string JSON válido
                if (is_array($contentVariables)) {
                    $contentVariables = json_encode($contentVariables);
                }
                $params['contentVariables'] = $contentVariables;
            }
            
            // Body es opcional cuando se usa plantilla, pero Twilio lo requiere
            // Usar un mensaje por defecto si no se proporciona
            if (empty($mensaje)) {
                $mensaje = 'Mensaje desde plantilla';
            }
        }
        
        // Agregar body si se proporciona mensaje
        if (!empty($mensaje)) {
            $params['body'] = $mensaje;
        }
        
        // Enviar mensaje usando el SDK
        $message = $twilio->messages->create($numeroFormateado, $params);
        
        return [
            'message_id' => $message->sid,
            'status' => $message->status ?? 'queued',
            'body' => $message->body ?? null,
            'date_created' => $message->dateCreated ? $message->dateCreated->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Enviar mensaje usando WhatsApp Business API
     */
    protected function enviarPorWhatsAppBusiness($numeroDestino, $mensaje)
    {
        $config = $this->config['whatsapp_business'];
        
        if (empty($config['access_token']) || empty($config['phone_number_id'])) {
            throw new \Exception('Configuración de WhatsApp Business API incompleta');
        }
        
        // Formatear número para WhatsApp Business API (56912345678)
        $numeroFormateado = $this->formatearNumeroWhatsAppBusiness($numeroDestino);
        
        $response = $this->httpClient->request('POST', $config['api_url'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $config['access_token'],
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'messaging_product' => 'whatsapp',
                'to' => $numeroFormateado,
                'type' => 'text',
                'text' => [
                    'body' => $mensaje
                ]
            ]
        ]);
        
        $body = json_decode($response->getBody(), true);
        
        return [
            'message_id' => $body['messages'][0]['id'] ?? null,
            'status' => 'sent'
        ];
    }

    /**
     * Enviar confirmación de cita
     */
    public function enviarConfirmacionCita($detalleAgendaId, $pacienteId = null, $meetLink = null)
    {
        log_message('error', 'WHATSAPP SERVICE - enviarConfirmacionCita: INICIO');
        log_message('error', 'WHATSAPP SERVICE: detalleAgendaId=' . $detalleAgendaId . ', pacienteId=' . ($pacienteId ?? 'N/A') . ', meetLink=' . ($meetLink ? 'SÍ' : 'NO'));
        
        $db = \Config\Database::connect();
        
        // Obtener información de la cita
        log_message('error', 'WHATSAPP SERVICE: Ejecutando consulta para obtener información de la cita');
        $cita = $db->table('detalle_agenda da')
            ->select('da.*, a.fecha, p.nombre, p.apellido, p.telefono, u.nombre as nutricionista_nombre, ma.nombre as modalidad')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('usuario u', 'u.id = da.usuario_id', 'left')
            ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
            ->where('da.id', $detalleAgendaId)
            ->get()
            ->getRow();
        
        log_message('error', 'WHATSAPP SERVICE: Consulta ejecutada. cita encontrada=' . ($cita ? 'SÍ' : 'NO'));
        
        if (!$cita) {
            log_message('error', 'WHATSAPP SERVICE: Cita no encontrada. detalleAgendaId=' . $detalleAgendaId);
            return ['success' => false, 'error' => 'Cita no encontrada'];
        }
        
        log_message('error', 'WHATSAPP SERVICE: Datos de la cita obtenidos:');
        log_message('error', '  - telefono=' . ($cita->telefono ?? 'N/A'));
        log_message('error', '  - nombre=' . ($cita->nombre ?? 'N/A') . ' ' . ($cita->apellido ?? 'N/A'));
        log_message('error', '  - fecha=' . ($cita->fecha ?? 'N/A'));
        log_message('error', '  - modalidad=' . ($cita->modalidad ?? 'N/A'));
        log_message('error', '  - paciente_id=' . ($cita->paciente_id ?? 'N/A'));
        
        if (!$cita->telefono) {
            log_message('error', 'WHATSAPP SERVICE: Paciente sin teléfono. paciente_id=' . ($cita->paciente_id ?? 'N/A'));
            return ['success' => false, 'error' => 'Paciente sin teléfono'];
        }
        
        $fecha = $cita->fecha ?? $cita->fecha_agenda;
        $horaInicio = date('H:i', strtotime($cita->hora_inicio));
        $nombrePaciente = trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? ''));
        $nutricionista = $cita->nutricionista_nombre ?? 'Nutricionista';
        
        // Determinar si es modalidad online
        $modalidadLower = strtolower($cita->modalidad ?? '');
        $esOnline = (stripos($modalidadLower, 'online') !== false || 
                    (stripos($modalidadLower, 'presencial') === false && !empty($modalidadLower)));
        
        log_message('error', 'WHATSAPP SERVICE: Preparando mensaje. esOnline=' . ($esOnline ? 'SÍ' : 'NO') . ', meetLink=' . ($meetLink ? 'SÍ' : 'NO'));
        
        // Mensaje simple e informativo (la confirmación se hace desde el email)
        $mensaje = "¡Hola {$nombrePaciente}!\n\n";
        $mensaje .= "Tu cita con {$nutricionista} ha sido agendada:\n\n";
        $mensaje .= "📅 Fecha: {$fecha}\n";
        $mensaje .= "🕐 Hora: {$horaInicio}\n";
        if ($cita->tipo_consulta) {
            $mensaje .= "📋 Tipo: {$cita->tipo_consulta}\n";
        }
        
        // Agregar enlace de Google Meet si es modalidad online y tenemos el enlace
        if ($esOnline && !empty($meetLink)) {
            $mensaje .= "\n🔗 Enlace de Google Meet:\n";
            $mensaje .= "{$meetLink}\n";
            $mensaje .= "\n💡 Te recomendamos unirte unos minutos antes para verificar tu conexión.";
        }
        
        $mensaje .= "\n¡Te esperamos!";
        
        log_message('error', 'WHATSAPP SERVICE: Mensaje preparado. Longitud=' . strlen($mensaje) . ' caracteres');
        log_message('error', 'WHATSAPP SERVICE: Llamando a enviarMensaje()');
        log_message('error', 'WHATSAPP SERVICE: Parámetros: telefono=' . $cita->telefono . ', pacienteId=' . ($pacienteId ?? $cita->paciente_id ?? 'N/A') . ', agendaId=' . ($cita->agenda_id ?? 'N/A') . ', usuarioId=' . ($cita->usuario_id ?? 'N/A'));
        
        $resultado = $this->enviarMensaje(
            $cita->telefono,
            $mensaje,
            $pacienteId ?? $cita->paciente_id,
            $cita->agenda_id ?? null,
            $cita->usuario_id ?? null
        );
        
        log_message('error', 'WHATSAPP SERVICE: Resultado de enviarMensaje: success=' . ($resultado['success'] ?? 'N/A'));
        if (isset($resultado['error'])) {
            log_message('error', 'WHATSAPP SERVICE: Error en enviarMensaje: ' . $resultado['error']);
        }
        log_message('error', 'WHATSAPP SERVICE - enviarConfirmacionCita: FIN');
        
        return $resultado;
    }

    /**
     * Enviar notificación de cancelación de cita por WhatsApp
     */
    public function enviarCancelacionCita($detalleAgendaId, $pacienteId = null)
    {
        $db = \Config\Database::connect();
        
        // Obtener información de la cita
        // IMPORTANTE: Si paciente_id fue puesto en null, usar el parámetro $pacienteId
        $query = $db->table('detalle_agenda da')
            ->select('da.*, da.agenda_id, a.fecha, a.usuario_id as agenda_usuario_id, p.nombre, p.apellido, p.telefono, u.nombre as nutricionista_nombre')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('usuario u', 'u.id = COALESCE(da.usuario_id, a.usuario_id)', 'left')
            ->where('da.id', $detalleAgendaId);
        
        // Si tenemos pacienteId como parámetro, usarlo para obtener datos del paciente
        if ($pacienteId) {
            $query->join('pacientes p', 'p.id = ' . (int)$pacienteId, 'left');
        } else {
            // Intentar obtener desde detalle_agenda (puede ser null si ya se canceló)
            $query->join('pacientes p', 'p.id = da.paciente_id', 'left');
        }
        
        $cita = $query->get()->getRow();
        
        log_message('info', 'WhatsApp Cancelación: Consulta inicial. cita encontrada=' . ($cita ? 'SÍ' : 'NO') . ', telefono=' . ($cita->telefono ?? 'N/A') . ', pacienteId=' . $pacienteId);
        
        // Si no encontramos el paciente en la consulta pero tenemos pacienteId, buscarlo directamente
        if ((!$cita || !$cita->telefono) && $pacienteId) {
            $paciente = $db->table('pacientes')
                ->where('id', $pacienteId)
                ->get()
                ->getRow();
            
            if ($paciente) {
                // Obtener información básica de la cita sin el paciente
                $citaBasica = $db->table('detalle_agenda da')
                    ->select('da.*, a.fecha, a.usuario_id as agenda_usuario_id, u.nombre as nutricionista_nombre')
                    ->join('agenda a', 'a.id = da.agenda_id', 'left')
                    ->join('usuario u', 'u.id = COALESCE(da.usuario_id, a.usuario_id)', 'left')
                    ->where('da.id', $detalleAgendaId)
                    ->get()
                    ->getRow();
                
                if ($citaBasica) {
                    // Combinar datos
                    $cita = (object) array_merge((array) $citaBasica, [
                        'nombre' => $paciente->nombre,
                        'apellido' => $paciente->apellido,
                        'telefono' => $paciente->telefono,
                        'paciente_id_db' => $paciente->id
                    ]);
                    log_message('info', 'WhatsApp Cancelación: Datos combinados. Telefono=' . $paciente->telefono);
                }
            }
        }
        
        if (!$cita || !$cita->telefono) {
            log_message('error', 'WhatsApp Cancelación: Cita no encontrada o paciente sin teléfono. detalleAgendaId=' . $detalleAgendaId . ', pacienteId=' . $pacienteId);
            return ['success' => false, 'error' => 'Cita no encontrada o paciente sin teléfono'];
        }
        
        $fecha = $cita->fecha ?? $cita->fecha_agenda ?? null;
        $horaInicio = !empty($cita->hora_inicio) ? date('H:i', strtotime($cita->hora_inicio)) : 'N/A';
        $nombrePaciente = trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? ''));
        $nutricionista = $cita->nutricionista_nombre ?? 'Nutricionista';
        
        // Obtener usuario_id correctamente (desde agenda_usuario_id o usuario_id)
        $usuarioId = $cita->agenda_usuario_id ?? $cita->usuario_id ?? null;
        
        // Mensaje de cancelación
        $mensaje = "Hola {$nombrePaciente}\n\n";
        $mensaje .= "Te informamos que tu cita con {$nutricionista} ha sido cancelada:\n\n";
        if ($fecha) {
            $mensaje .= "📅 Fecha: {$fecha}\n";
        }
        $mensaje .= "🕐 Hora: {$horaInicio}\n";
        $mensaje .= "\nSi necesitas reagendar, por favor contacta con tu nutricionista.\n\n";
        $mensaje .= "¡Gracias por tu comprensión!";
        
        log_message('info', 'WhatsApp Cancelación: Enviando mensaje. Telefono=' . $cita->telefono . ', pacienteId=' . ($pacienteId ?? 'N/A') . ', usuarioId=' . ($usuarioId ?? 'N/A'));
        
        return $this->enviarMensaje(
            $cita->telefono,
            $mensaje,
            $pacienteId ?? $cita->paciente_id_db ?? null,
            $cita->agenda_id ?? null,
            $usuarioId
        );
        
        // NOTA: Si prefieres usar plantilla de Twilio, descomenta el código de abajo
        // y comenta el código de arriba. También necesitarás configurar TWILIO_CONTENT_SID_CONFIRMACION en .env
        /*
        $contentSid = $this->config['twilio']['content_sid_confirmacion'] ?? null;
        
        if ($contentSid) {
            // Usar plantilla de Twilio
            $fechaFormateada = $this->formatearFechaParaPlantilla($fecha);
            $horaFormateada = $horaInicio;
            
            $contentVariables = json_encode([
                "1" => $fechaFormateada,
                "2" => $horaFormateada
            ]);
            
            return $this->enviarMensaje(
                $cita->telefono,
                null,
                $pacienteId ?? $cita->paciente_id,
                $cita->agenda_id ?? null,
                $cita->usuario_id ?? null,
                $contentSid,
                $contentVariables
            );
        } else {
            // Mensaje de texto simple
            $mensaje = "¡Hola {$nombrePaciente}!\n\n";
            $mensaje .= "Tu cita con {$nutricionista} ha sido confirmada:\n\n";
            $mensaje .= "📅 Fecha: {$fecha}\n";
            $mensaje .= "🕐 Hora: {$horaInicio}\n";
            if ($cita->tipo_consulta) {
                $mensaje .= "📋 Tipo: {$cita->tipo_consulta}\n";
            }
            $mensaje .= "\n¡Te esperamos!\n";
            $mensaje .= "Si necesitas cancelar o reagendar, responde a este mensaje.";
            
            return $this->enviarMensaje(
                $cita->telefono,
                $mensaje,
                $pacienteId ?? $cita->paciente_id,
                $cita->agenda_id ?? null,
                $cita->usuario_id ?? null
            );
        }
        */
    }

    /**
     * Enviar recordatorio de cita
     */
    public function enviarRecordatorioCita($detalleAgendaId, $horasAntes = 24)
    {
        $db = \Config\Database::connect();
        
        // Obtener citas que están en las próximas X horas
        $citas = $db->table('detalle_agenda da')
            ->select('da.*, a.fecha, p.nombre, p.apellido, p.telefono, u.nombre as nutricionista_nombre')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('usuario u', 'u.id = da.usuario_id', 'left')
            ->where('da.id', $detalleAgendaId)
            ->where('da.estado_cita', 'confirmada')
            ->where('da.paciente_id IS NOT NULL')
            ->get()
            ->getResult();
        
        $enviados = [];
        
        foreach ($citas as $cita) {
            if (!$cita->telefono) {
                continue;
            }
            
            $fecha = $cita->fecha ?? $cita->fecha_agenda;
            $horaInicio = date('H:i', strtotime($cita->hora_inicio));
            $nombrePaciente = trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? ''));
            $nutricionista = $cita->nutricionista_nombre ?? 'Nutricionista';
            
            $mensaje = "🔔 Recordatorio de Cita\n\n";
            $mensaje .= "Hola {$nombrePaciente},\n\n";
            $mensaje .= "Te recordamos tu cita con {$nutricionista}:\n\n";
            $mensaje .= "📅 Fecha: {$fecha}\n";
            $mensaje .= "🕐 Hora: {$horaInicio}\n";
            $mensaje .= "\n¡Nos vemos pronto!\n";
            $mensaje .= "Si necesitas cancelar o reagendar, responde a este mensaje.";
            
            $resultado = $this->enviarMensaje(
                $cita->telefono,
                $mensaje,
                $cita->paciente_id,
                $cita->agenda_id ?? null,
                $cita->usuario_id ?? null
            );
            
            $enviados[] = $resultado;
        }
        
        return $enviados;
    }

    /**
     * Procesar mensaje entrante y crear cita automáticamente
     */
    public function procesarMensajeEntrante($numeroOrigen, $mensajeTexto, $mensajeId = null)
    {
        // Registrar mensaje recibido
        $this->whatsappModel->insert([
            'direccion' => 'recibido',
            'numero_origen' => $numeroOrigen,
            'numero_destino' => $this->getNumeroOrigen(),
            'mensaje' => $mensajeTexto,
            'mensaje_id_api' => $mensajeId,
            'estado_envio' => 'recibido',
            'tipo_mensaje' => 'agendamiento',
            'fecha_envio' => date('Y-m-d H:i:s'),
            'fcreacion' => date('Y-m-d H:i:s') // Agregar manualmente el timestamp de creación
        ]);
        
        // Buscar paciente por teléfono
        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->where('telefono', $numeroOrigen)
            ->orWhere('telefono', $this->normalizarNumero($numeroOrigen))
            ->first();
        
        if (!$paciente) {
            // Responder que no está registrado
            return $this->enviarMensaje(
                $numeroOrigen,
                "Hola, no encontramos tu número en nuestro sistema. Por favor, contacta directamente con tu nutricionista para agendar una cita."
            );
        }
        
        // Procesar respuestas de botones (Confirm/Cancel)
        $mensajeNormalizado = strtolower(trim($mensajeTexto));
        if ($mensajeNormalizado === 'confirm' || $mensajeNormalizado === 'confirmar') {
            return $this->procesarConfirmacionCita($numeroOrigen, $paciente->id);
        } elseif ($mensajeNormalizado === 'cancel' || $mensajeNormalizado === 'cancelar') {
            return $this->procesarCancelacionCita($numeroOrigen, $paciente->id);
        }
        
        // Intentar extraer información de la cita del mensaje
        $infoCita = $this->extraerInfoCitaDelMensaje($mensajeTexto);
        
        if ($infoCita) {
            // Buscar horario disponible
            $detalleAgendaId = $this->buscarHorarioDisponible($infoCita, $paciente->id);
            
            if ($detalleAgendaId) {
                // Crear cita
                $this->crearCitaDesdeWhatsApp($detalleAgendaId, $paciente->id, $infoCita);
                
                // Enviar confirmación
                $this->enviarConfirmacionCita($detalleAgendaId, $paciente->id);
                
                return [
                    'success' => true,
                    'message' => 'Cita creada y confirmación enviada',
                    'detalle_agenda_id' => $detalleAgendaId
                ];
            } else {
                // No hay horario disponible
                return $this->enviarMensaje(
                    $numeroOrigen,
                    "Hola {$paciente->nombre}, no encontramos un horario disponible para la fecha y hora que solicitas. Por favor, contacta directamente con tu nutricionista.",
                    $paciente->id
                );
            }
        } else {
            // Mensaje no reconocido, ofrecer ayuda
            return $this->enviarMensaje(
                $numeroOrigen,
                "Hola {$paciente->nombre}, para agendar una cita, envía un mensaje con la fecha y hora deseada. Ejemplo: 'Quiero agendar para el 15 de enero a las 10:00'",
                $paciente->id
            );
        }
    }

    /**
     * Extraer información de cita del mensaje de texto
     */
    protected function extraerInfoCitaDelMensaje($mensaje)
    {
        $mensaje = strtolower($mensaje);
        $info = [];
        
        // Buscar fecha (día del mes)
        if (preg_match('/(\d{1,2})\s*(?:de\s*)?(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre)/i', $mensaje, $matches)) {
            $meses = [
                'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4,
                'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8,
                'septiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12
            ];
            $dia = (int)$matches[1];
            $mes = $meses[strtolower($matches[2])];
            $anio = date('Y');
            
            // Si el mes ya pasó, usar el próximo año
            if ($mes < (int)date('m') || ($mes == (int)date('m') && $dia < (int)date('d'))) {
                $anio++;
            }
            
            $info['fecha'] = sprintf('%02d-%02d-%04d', $dia, $mes, $anio);
        }
        
        // Buscar hora (formato HH:MM o H:MM)
        if (preg_match('/(\d{1,2}):(\d{2})/', $mensaje, $matches)) {
            $info['hora'] = sprintf('%02d:%02d', $matches[1], $matches[2]);
        }
        
        return !empty($info) ? $info : null;
    }

    /**
     * Buscar horario disponible
     */
    protected function buscarHorarioDisponible($infoCita, $pacienteId)
    {
        $db = \Config\Database::connect();
        
        $fecha = $infoCita['fecha'] ?? null;
        $hora = $infoCita['hora'] ?? null;
        
        if (!$fecha) {
            return null;
        }
        
        $builder = $db->table('detalle_agenda da')
            ->select('da.id')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->where('a.fecha', $fecha)
            ->where('da.estado', 1) // Disponible
            ->where('da.paciente_id IS NULL');
        
        if ($hora) {
            $builder->where('da.hora_inicio', $hora . ':00');
        }
        
        $detalle = $builder->orderBy('da.hora_inicio', 'ASC')
            ->limit(1)
            ->get()
            ->getRow();
        
        return $detalle ? $detalle->id : null;
    }

    /**
     * Crear cita desde WhatsApp
     */
    protected function crearCitaDesdeWhatsApp($detalleAgendaId, $pacienteId, $infoCita)
    {
        $db = \Config\Database::connect();
        
        $dataUpdate = [
            'paciente_id' => $pacienteId,
            'estado' => 2, // Ocupado
            'estado_cita' => 'pendiente',
            'tipo_consulta' => $infoCita['tipo_consulta'] ?? 'control',
            'motivo' => 'Agendado por WhatsApp',
            'forma_asignacion' => 'WhatsApp'
        ];
        
        $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->update($dataUpdate);
        
        return $detalleAgendaId;
    }

    /**
     * Normalizar número de teléfono
     */
    protected function normalizarNumero($numero)
    {
        // Eliminar espacios, guiones, paréntesis
        $numero = preg_replace('/[\s\-\(\)]/', '', $numero);
        
        // Si empieza con +, mantenerlo
        if (substr($numero, 0, 1) === '+') {
            return $numero;
        }
        
        // Si empieza con 0, reemplazar con código de país (Chile: +56)
        if (substr($numero, 0, 1) === '0') {
            $numero = '+56' . substr($numero, 1);
        } elseif (substr($numero, 0, 2) === '56') {
            $numero = '+' . $numero;
        } elseif (strlen($numero) === 9) {
            // Número chileno sin código de país
            $numero = '+56' . $numero;
        }
        
        return $numero;
    }

    /**
     * Formatear número para Twilio
     */
    protected function formatearNumeroTwilio($numero)
    {
        $numero = $this->normalizarNumero($numero);
        return 'whatsapp:' . $numero;
    }

    /**
     * Formatear número para WhatsApp Business API
     */
    protected function formatearNumeroWhatsAppBusiness($numero)
    {
        $numero = $this->normalizarNumero($numero);
        // Eliminar el +
        return ltrim($numero, '+');
    }

    /**
     * Obtener número de origen
     */
    protected function getNumeroOrigen()
    {
        if ($this->provider === 'twilio') {
            return $this->config['twilio']['from_number'] ?? null;
        } elseif ($this->provider === 'whatsapp_business') {
            return $this->config['whatsapp_business']['phone_number_id'] ?? null;
        }
        return null;
    }

    /**
     * Determinar tipo de mensaje
     */
    protected function determinarTipoMensaje($mensaje)
    {
        $mensaje = strtolower($mensaje);
        
        if (strpos($mensaje, 'confirm') !== false || strpos($mensaje, 'confirmación') !== false) {
            return 'confirmacion';
        } elseif (strpos($mensaje, 'recordatorio') !== false || strpos($mensaje, 'recordar') !== false) {
            return 'recordatorio';
        } elseif (strpos($mensaje, 'cancel') !== false || strpos($mensaje, 'cancelación') !== false) {
            return 'cancelacion';
        } elseif (strpos($mensaje, 'agendar') !== false || strpos($mensaje, 'cita') !== false) {
            return 'agendamiento';
        }
        
        return 'otro';
    }

    /**
     * Formatear fecha para plantilla de Twilio
     * Convierte DD-MM-YYYY a formato para plantilla (ej: "12/1" para 12 de enero)
     */
    protected function formatearFechaParaPlantilla($fecha)
    {
        // Si la fecha está en formato DD-MM-YYYY
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fecha, $matches)) {
            $dia = (int)$matches[1];
            $mes = (int)$matches[2];
            return "{$dia}/{$mes}";
        }
        
        // Si está en otro formato, intentar parsear
        $timestamp = strtotime(str_replace('-', '/', $fecha));
        if ($timestamp) {
            return date('j/n', $timestamp); // j = día sin cero inicial, n = mes sin cero inicial
        }
        
        return $fecha; // Devolver original si no se puede parsear
    }

    /**
     * Procesar confirmación de cita desde WhatsApp
     */
    protected function procesarConfirmacionCita($numeroOrigen, $pacienteId)
    {
        $db = \Config\Database::connect();
        
        // Buscar la cita más reciente pendiente del paciente
        $cita = $db->table('detalle_agenda da')
            ->select('da.id, da.estado_cita, a.fecha, da.hora_inicio')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->where('da.paciente_id', $pacienteId)
            ->where('da.estado_cita', 'pendiente')
            ->orderBy('a.fecha', 'DESC')
            ->orderBy('da.hora_inicio', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
        
        if (!$cita) {
            return $this->enviarMensaje(
                $numeroOrigen,
                "No encontramos una cita pendiente para confirmar. Por favor, contacta directamente con tu nutricionista.",
                $pacienteId
            );
        }
        
        // Confirmar la cita
        $db->table('detalle_agenda')
            ->where('id', $cita->id)
            ->update([
                'estado_cita' => 'confirmada',
                'fecha_confirmacion' => date('Y-m-d H:i:s')
            ]);
        
        // Enviar confirmación
        $fecha = $cita->fecha;
        $hora = date('H:i', strtotime($cita->hora_inicio));
        
        return $this->enviarMensaje(
            $numeroOrigen,
            "✅ ¡Cita confirmada!\n\n📅 Fecha: {$fecha}\n🕐 Hora: {$hora}\n\n¡Te esperamos!",
            $pacienteId,
            null,
            null
        );
    }

    /**
     * Procesar cancelación de cita desde WhatsApp
     */
    protected function procesarCancelacionCita($numeroOrigen, $pacienteId)
    {
        $db = \Config\Database::connect();
        
        // Buscar la cita más reciente del paciente (pendiente o confirmada)
        $cita = $db->table('detalle_agenda da')
            ->select('da.id, da.estado_cita, a.fecha, da.hora_inicio')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->where('da.paciente_id', $pacienteId)
            ->whereIn('da.estado_cita', ['pendiente', 'confirmada'])
            ->orderBy('a.fecha', 'DESC')
            ->orderBy('da.hora_inicio', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
        
        if (!$cita) {
            return $this->enviarMensaje(
                $numeroOrigen,
                "No encontramos una cita para cancelar. Por favor, contacta directamente con tu nutricionista.",
                $pacienteId
            );
        }
        
        // Cancelar la cita
        $db->table('detalle_agenda')
            ->where('id', $cita->id)
            ->update([
                'estado_cita' => 'cancelada',
                'fecha_cancelacion' => date('Y-m-d H:i:s'),
                'motivo_cancelacion' => 'Cancelada por WhatsApp',
                'paciente_id' => null, // Liberar el horario
                'estado' => 1 // Volver a disponible
            ]);
        
        // Enviar confirmación de cancelación
        $fecha = $cita->fecha;
        $hora = date('H:i', strtotime($cita->hora_inicio));
        
        return $this->enviarMensaje(
            $numeroOrigen,
            "❌ Cita cancelada\n\n📅 Fecha: {$fecha}\n🕐 Hora: {$hora}\n\nSi necesitas reagendar, puedes contactarnos nuevamente.",
            $pacienteId,
            null,
            null
        );
    }
}
