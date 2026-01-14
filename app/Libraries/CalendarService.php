<?php

namespace App\Libraries;

use Config\Services;

/**
 * Servicio para integrar citas con Google Calendar
 * Soporta creación, actualización y eliminación de eventos
 */
class CalendarService
{
    protected $provider; // 'google' o 'outlook'
    protected $config;
    protected $httpClient;
    protected $accessToken;

    public function __construct($usuarioId = null)
    {
        $this->httpClient = Services::curlrequest();
        
        // Cargar configuración desde .env
        $this->provider = env('CALENDAR_PROVIDER', 'google'); // 'google' o 'outlook'
        
        $this->config = [
            'google' => [
                'client_id' => env('GOOGLE_CALENDAR_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CALENDAR_CLIENT_SECRET'),
                'redirect_uri' => env('GOOGLE_CALENDAR_REDIRECT_URI', base_url('dashboard/agenda/calendario/callback')),
                'scopes' => 'https://www.googleapis.com/auth/calendar',
                'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_url' => 'https://oauth2.googleapis.com/token',
                'api_url' => 'https://www.googleapis.com/calendar/v3'
            ],
            'outlook' => [
                'client_id' => env('OUTLOOK_CALENDAR_CLIENT_ID'),
                'client_secret' => env('OUTLOOK_CALENDAR_CLIENT_SECRET'),
                'redirect_uri' => base_url('dashboard/agenda/calendario/callback'),
                'scopes' => 'https://graph.microsoft.com/Calendars.ReadWrite',
                'auth_url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
                'token_url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
                'api_url' => 'https://graph.microsoft.com/v1.0'
            ]
        ];
        
        // Cargar token de acceso del usuario si se proporciona
        if ($usuarioId) {
            log_message('info', 'CalendarService: Cargando token para usuario ID: ' . $usuarioId . ', provider: ' . $this->provider);
            $this->loadUserToken($usuarioId);
            log_message('info', 'CalendarService: Token cargado: ' . ($this->accessToken ? 'Sí' : 'NO'));
        }
    }

    /**
     * Establecer el proveedor de calendario
     */
    public function setProvider($provider)
    {
        if (in_array($provider, ['google', 'outlook'])) {
            $this->provider = $provider;
            log_message('info', 'Provider establecido a: ' . $this->provider);
        } else {
            log_message('warning', 'Provider inválido: ' . $provider . ', usando google por defecto');
            $this->provider = 'google';
        }
    }

    /**
     * Obtener URL de autorización OAuth2
     */
    public function getAuthUrl($usuarioId)
    {
        $config = $this->config[$this->provider];
        
        log_message('info', 'Generando URL de autorización para usuario ID: ' . $usuarioId);
        log_message('info', 'Provider: ' . $this->provider);
        log_message('info', 'Client ID: ' . (empty($config['client_id']) ? 'VACÍO' : substr($config['client_id'], 0, 20) . '...'));
        
        if (empty($config['client_id'])) {
            throw new \Exception('Configuración de calendario incompleta: falta client_id');
        }
        
        $redirectUri = $config['redirect_uri'];
        log_message('info', 'Redirect URI configurado: ' . $redirectUri);
        
        // Agregar timestamp para validar expiración
        $stateData = [
            'usuario_id' => $usuarioId,
            'provider' => $this->provider,
            'timestamp' => time() // Para validar que no sea muy antiguo
        ];
        $state = base64_encode(json_encode($stateData));
        log_message('info', 'State generado: ' . substr($state, 0, 30) . '...');
        
        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $config['scopes'],
            'access_type' => 'offline', // Para obtener refresh token
            'prompt' => 'consent',
            'state' => $state
        ];
        
        $authUrl = $config['auth_url'] . '?' . http_build_query($params);
        log_message('info', 'URL de autorización completa: ' . $authUrl);
        
        return $authUrl;
    }

    /**
     * Intercambiar código de autorización por token de acceso
     */
    public function exchangeCodeForToken($code, $usuarioId)
    {
        $config = $this->config[$this->provider];
        
        $data = [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri' => $config['redirect_uri'],
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];
        
        // Configurar opciones de cURL para SSL en desarrollo
        // Verificar variable de entorno
        $disableSsl = (ENVIRONMENT === 'development' && env('GOOGLE_DISABLE_SSL_VERIFY', false));
        log_message('info', 'GOOGLE_DISABLE_SSL_VERIFY: ' . ($disableSsl ? 'true' : 'false') . ', ENVIRONMENT: ' . ENVIRONMENT);
        
        // Preparar opciones para la petición
        $requestOptions = [
            'form_params' => $data,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ];
        
        if ($disableSsl) {
            log_message('warning', 'GOOGLE_DISABLE_SSL_VERIFY está activo. Deshabilitando verificación SSL para Google OAuth.');
            // CodeIgniter usa 'verify' para SSL (false = deshabilitar)
            $requestOptions['verify'] = false;
        } else {
            // Intentar encontrar el bundle de certificados CA
            $caBundlePath = $this->findCaBundle();
            if ($caBundlePath) {
                // CodeIgniter acepta string (ruta al archivo) o bool (true/false)
                $requestOptions['verify'] = $caBundlePath;
                log_message('info', 'Usando CA bundle para Google OAuth: ' . $caBundlePath);
            } else {
                // Si no hay CA bundle y estamos en desarrollo, deshabilitar SSL
                if (ENVIRONMENT === 'development') {
                    log_message('warning', 'No se encontró CA bundle. Deshabilitando verificación SSL para desarrollo.');
                    $requestOptions['verify'] = false;
                } else {
                    log_message('warning', 'No se encontró CA bundle. La verificación SSL de Google OAuth podría fallar.');
                }
            }
        }
        
        log_message('info', 'Opciones de request: verify=' . (is_bool($requestOptions['verify']) ? ($requestOptions['verify'] ? 'true' : 'false') : $requestOptions['verify']));
        
        $response = $this->httpClient->request('POST', $config['token_url'], $requestOptions);
        $statusCode = $response->getStatusCode();
        
        // En CodeIgniter 4, getBody() puede devolver un string directamente o un objeto stream
        $bodyObj = $response->getBody();
        if (is_string($bodyObj)) {
            $body = $bodyObj;
        } else {
            $body = $bodyObj->getContents();
        }
        
        $tokenData = json_decode($body, true);
        
        log_message('info', 'Respuesta de exchangeCodeForToken - Status: ' . $statusCode);
        log_message('info', 'Body completo de respuesta: ' . $body);
        
        if ($statusCode !== 200) {
            $errorMsg = $tokenData['error'] ?? 'Error desconocido';
            $errorDesc = $tokenData['error_description'] ?? 'Sin descripción';
            log_message('error', 'Error al obtener token: ' . $errorMsg . ' - ' . $errorDesc);
            throw new \Exception('Error al obtener token: ' . $errorMsg . ' - ' . $errorDesc);
        }
        
        if (isset($tokenData['error'])) {
            log_message('error', 'Error en respuesta de token: ' . $tokenData['error'] . ' - ' . ($tokenData['error_description'] ?? 'Sin descripción'));
            throw new \Exception('Error al obtener token: ' . ($tokenData['error_description'] ?? $tokenData['error']));
        }
        
        // Logging detallado de lo que recibimos
        log_message('info', 'Token recibido de Google:');
        log_message('info', '  - access_token: ' . (isset($tokenData['access_token']) ? 'SÍ (' . substr($tokenData['access_token'], 0, 20) . '...)' : 'NO'));
        log_message('info', '  - refresh_token: ' . (isset($tokenData['refresh_token']) ? 'SÍ (' . substr($tokenData['refresh_token'], 0, 20) . '...)' : 'NO'));
        log_message('info', '  - expires_in: ' . ($tokenData['expires_in'] ?? 'NO'));
        log_message('info', '  - token_type: ' . ($tokenData['token_type'] ?? 'NO'));
        log_message('info', '  - scope: ' . ($tokenData['scope'] ?? 'NO'));
        
        if (!isset($tokenData['access_token'])) {
            log_message('error', 'No se recibió access_token en la respuesta');
            throw new \Exception('No se recibió access_token de Google');
        }
        
        // Guardar token en base de datos
        log_message('info', 'Guardando token en base de datos para usuario ID: ' . $usuarioId);
        $this->saveUserToken($usuarioId, $tokenData);
        
        $this->accessToken = $tokenData['access_token'];
        
        log_message('info', 'Token guardado exitosamente. Access token establecido en memoria.');
        
        return $tokenData;
    }

    /**
     * Crear evento en el calendario
     */
    public function crearEvento($detalleAgendaId, $citaData)
    {
        // Asegurar que tenemos un token válido
        if (!$this->accessToken) {
            // Intentar cargar el token si no está cargado
            if (isset($citaData['usuario_id'])) {
                $this->loadUserToken($citaData['usuario_id']);
            }
            
            if (!$this->accessToken) {
                throw new \Exception('No hay token de acceso. El usuario debe autorizar primero.');
            }
        }
        
        // Verificar que el token no esté expirado y refrescarlo si es necesario
        $this->verificarYRefrescarToken($citaData['usuario_id'] ?? null);
        
        if ($this->provider === 'google') {
            return $this->crearEventoGoogle($citaData);
        } elseif ($this->provider === 'outlook') {
            return $this->crearEventoOutlook($citaData);
        }
        
        throw new \Exception('Proveedor de calendario no soportado');
    }

    /**
     * Crear evento en Google Calendar
     */
    protected function crearEventoGoogle($citaData)
    {
        $config = $this->config['google'];
        
        // Convertir fecha DD-MM-YYYY a formato ISO 8601
        $fechaISO = $this->convertirFechaAISO($citaData['fecha'], $citaData['hora_inicio'], $citaData['hora_fin']);
        
        // Determinar si es modalidad online (case-insensitive, busca "online" en el nombre)
        $modalidadLower = strtolower($citaData['modalidad'] ?? '');
        $esOnline = (stripos($modalidadLower, 'online') !== false || 
                    (stripos($modalidadLower, 'presencial') === false && !empty($modalidadLower)));
        
        $evento = [
            'summary' => 'Consulta: ' . ($citaData['nombre_paciente'] ?? 'Paciente'),
            'description' => $this->generarDescripcionEvento($citaData),
            'start' => [
                'dateTime' => $fechaISO['inicio'],
                'timeZone' => 'America/Santiago'
            ],
            'end' => [
                'dateTime' => $fechaISO['fin'],
                'timeZone' => 'America/Santiago'
            ],
            'location' => $citaData['modalidad'] === 'Presencial' ? 'Consultorio' : 'Online',
            'attendees' => [
                [
                    'email' => $citaData['email_paciente'] ?? null,
                    'displayName' => $citaData['nombre_paciente'] ?? 'Paciente'
                ]
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60], // 24 horas antes
                    ['method' => 'popup', 'minutes' => 60] // 1 hora antes
                ]
            ]
        ];
        
        // Agregar Google Meet si la modalidad es Online
        if ($esOnline) {
            // Generar un requestId único para la solicitud de conferencia
            $requestId = 'meet-' . ($citaData['detalle_agenda_id'] ?? uniqid()) . '-' . time();
            
            $evento['conferenceData'] = [
                'createRequest' => [
                    'requestId' => $requestId,
                    'conferenceSolutionKey' => [
                        'type' => 'hangoutsMeet'
                    ]
                ]
            ];
            
            log_message('info', 'Agregando Google Meet al evento (modalidad Online). RequestId: ' . $requestId);
        }
        
        // Eliminar attendees si no hay email
        if (empty($evento['attendees'][0]['email'])) {
            unset($evento['attendees']);
        }
        
        // Validar que tenemos un access token
        if (empty($this->accessToken)) {
            log_message('error', 'Access token está vacío antes de crear evento');
            throw new \Exception('Access token no disponible. El usuario debe re-autorizar.');
        }
        
        $requestOptions = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ],
            'json' => $evento,
            'http_errors' => false // No lanzar excepción automáticamente para poder capturar el body del error
        ];
        
        // Agregar opciones SSL
        $this->agregarOpcionesSSL($requestOptions);
        
        // Agregar parámetro conferenceDataVersion=1 si se está creando una conferencia
        $url = $config['api_url'] . '/calendars/primary/events';
        if ($esOnline && isset($evento['conferenceData'])) {
            $url .= '?conferenceDataVersion=1';
            log_message('info', 'Creando evento en Google Calendar CON Google Meet');
        } else {
            log_message('info', 'Creando evento en Google Calendar SIN Google Meet');
        }
        
        log_message('info', 'URL: ' . $url);
        log_message('info', 'Access token presente: ' . ($this->accessToken ? 'Sí (' . substr($this->accessToken, 0, 20) . '...)' : 'NO'));
        log_message('info', 'Access token completo (primeros 50 chars): ' . substr($this->accessToken, 0, 50));
        log_message('info', 'Headers: ' . json_encode($requestOptions['headers']));
        log_message('info', 'Evento JSON: ' . json_encode($evento));
        
        try {
            $response = $this->httpClient->request('POST', $url, $requestOptions);
            
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody();
            $eventoData = json_decode($responseBody, true);
            
            log_message('info', 'Respuesta de Google Calendar API. Status: ' . $statusCode);
            log_message('info', 'Response body: ' . $responseBody);
            
            // Si hay error, loguearlo
            if ($statusCode >= 400) {
                log_message('error', 'Error de Google Calendar API (Status ' . $statusCode . '): ' . $responseBody);
                if (isset($eventoData['error'])) {
                    $errorMsg = $eventoData['error']['message'] ?? 'Error desconocido';
                    $errorCode = $eventoData['error']['code'] ?? $statusCode;
                    $errorReason = isset($eventoData['error']['errors'][0]['reason']) ? $eventoData['error']['errors'][0]['reason'] : 'unknown';
                    $errorDomain = isset($eventoData['error']['errors'][0]['domain']) ? $eventoData['error']['errors'][0]['domain'] : 'unknown';
                    log_message('error', 'Error details - Message: ' . $errorMsg . ', Code: ' . $errorCode . ', Reason: ' . $errorReason . ', Domain: ' . $errorDomain);
                    throw new \Exception('Error de Google Calendar: ' . $errorMsg . ' (Code: ' . $errorCode . ', Reason: ' . $errorReason . ')');
                }
                throw new \Exception('Error HTTP ' . $statusCode . ' al crear evento en Google Calendar. Response: ' . substr($responseBody, 0, 500));
            }
            
            // Guardar ID del evento en la base de datos
            if (isset($eventoData['id'])) {
                $this->guardarEventoId($citaData['detalle_agenda_id'], $eventoData['id']);
                log_message('info', 'Evento creado exitosamente. ID: ' . $eventoData['id']);
                
                // Si se creó una conferencia de Google Meet, guardar el enlace
                $meetLink = $this->extraerMeetLink($eventoData);
                if ($meetLink) {
                    log_message('info', 'Google Meet creado. Enlace: ' . $meetLink);
                    // Guardar el enlace de Meet en la base de datos si es necesario
                    // Por ahora solo lo logueamos, pero podrías guardarlo en detalle_agenda
                }
            }
            
            return [
                'success' => true,
                'event_id' => $eventoData['id'] ?? null,
                'html_link' => $eventoData['htmlLink'] ?? null,
                'meet_link' => $this->extraerMeetLink($eventoData) ?? null
            ];
        } catch (\CodeIgniter\HTTP\Exceptions\HTTPException $e) {
            log_message('error', 'Excepción HTTP al crear evento: ' . $e->getMessage());
            log_message('error', 'Código de error: ' . $e->getCode());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Intentar obtener más información del error
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $response = $e->getResponse();
                log_message('error', 'Response body: ' . $response->getBody());
                log_message('error', 'Response status: ' . $response->getStatusCode());
            }
            
            throw $e;
        } catch (\Exception $e) {
            log_message('error', 'Excepción al crear evento: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Crear evento en Outlook Calendar
     */
    protected function crearEventoOutlook($citaData)
    {
        $config = $this->config['outlook'];
        
        $fechaISO = $this->convertirFechaAISO($citaData['fecha'], $citaData['hora_inicio'], $citaData['hora_fin']);
        
        $evento = [
            'subject' => 'Consulta: ' . ($citaData['nombre_paciente'] ?? 'Paciente'),
            'body' => [
                'contentType' => 'HTML',
                'content' => $this->generarDescripcionEvento($citaData)
            ],
            'start' => [
                'dateTime' => $fechaISO['inicio'],
                'timeZone' => 'America/Santiago'
            ],
            'end' => [
                'dateTime' => $fechaISO['fin'],
                'timeZone' => 'America/Santiago'
            ],
            'location' => [
                'displayName' => $citaData['modalidad'] === 'Presencial' ? 'Consultorio' : 'Online'
            ],
            'attendees' => []
        ];
        
        if (!empty($citaData['email_paciente'])) {
            $evento['attendees'][] = [
                'emailAddress' => [
                    'address' => $citaData['email_paciente'],
                    'name' => $citaData['nombre_paciente'] ?? 'Paciente'
                ],
                'type' => 'required'
            ];
        }
        
        $requestOptions = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ],
            'json' => $evento
        ];
        
        // Agregar opciones SSL
        $this->agregarOpcionesSSL($requestOptions);
        
        $response = $this->httpClient->request('POST', $config['api_url'] . '/me/events', $requestOptions);
        
        $eventoData = json_decode($response->getBody(), true);
        
        if (isset($eventoData['id'])) {
            $this->guardarEventoId($citaData['detalle_agenda_id'], $eventoData['id']);
        }
        
        return [
            'success' => true,
            'event_id' => $eventoData['id'] ?? null,
            'webLink' => $eventoData['webLink'] ?? null
        ];
    }

    /**
     * Actualizar evento en el calendario
     */
    public function actualizarEvento($eventId, $citaData)
    {
        if (!$this->accessToken) {
            throw new \Exception('No hay token de acceso');
        }
        
        if ($this->provider === 'google') {
            return $this->actualizarEventoGoogle($eventId, $citaData);
        } elseif ($this->provider === 'outlook') {
            return $this->actualizarEventoOutlook($eventId, $citaData);
        }
    }

    /**
     * Eliminar evento del calendario
     */
    public function eliminarEvento($eventId)
    {
        if (!$this->accessToken) {
            throw new \Exception('No hay token de acceso');
        }
        
        $config = $this->config[$this->provider];
        
        if ($this->provider === 'google') {
            $url = $config['api_url'] . '/calendars/primary/events/' . $eventId;
        } else {
            $url = $config['api_url'] . '/me/events/' . $eventId;
        }
        
        $requestOptions = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken
            ]
        ];
        
        // Agregar opciones SSL
        $this->agregarOpcionesSSL($requestOptions);
        
        $response = $this->httpClient->request('DELETE', $url, $requestOptions);
        
        return $response->getStatusCode() === 204 || $response->getStatusCode() === 200;
    }

    /**
     * Convertir fecha DD-MM-YYYY y hora a formato ISO 8601
     */
    protected function convertirFechaAISO($fecha, $horaInicio, $horaFin)
    {
        // Convertir DD-MM-YYYY a YYYY-MM-DD
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fecha, $matches)) {
            $fechaISO = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        } else {
            $fechaISO = date('Y-m-d', strtotime($fecha));
        }
        
        // Extraer hora y minutos
        $horaInicioParts = explode(':', $horaInicio);
        $horaFinParts = explode(':', $horaFin);
        
        $inicioISO = $fechaISO . 'T' . $horaInicioParts[0] . ':' . ($horaInicioParts[1] ?? '00') . ':00';
        $finISO = $fechaISO . 'T' . $horaFinParts[0] . ':' . ($horaFinParts[1] ?? '00') . ':00';
        
        return [
            'inicio' => $inicioISO,
            'fin' => $finISO
        ];
    }

    /**
     * Generar descripción del evento
     */
    protected function generarDescripcionEvento($citaData)
    {
        $descripcion = "Consulta con {$citaData['nombre_nutricionista']}";
        
        // Agregar email del nutricionista si está disponible
        if (!empty($citaData['email_nutricionista'])) {
            $descripcion .= " ({$citaData['email_nutricionista']})";
        }
        
        $descripcion .= "\n\n";
        $descripcion .= "Paciente: {$citaData['nombre_paciente']}\n";
        $descripcion .= "Tipo: {$citaData['tipo_consulta']}\n";
        $descripcion .= "Modalidad: {$citaData['modalidad']}\n";
        
        if (!empty($citaData['motivo'])) {
            $descripcion .= "Motivo: {$citaData['motivo']}\n";
        }
        
        $descripcion .= "\nCreado desde NextLine Agenda";
        
        return $descripcion;
    }

    /**
     * Guardar ID del evento en la base de datos
     */
    protected function guardarEventoId($detalleAgendaId, $eventId)
    {
        $db = \Config\Database::connect();
        
        // Verificar si existe columna calendar_event_id en detalle_agenda
        // Si no existe, la crearemos con un script SQL
        $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->update(['calendar_event_id' => $eventId]);
    }

    /**
     * Cargar token de acceso del usuario
     */
    protected function loadUserToken($usuarioId)
    {
        $db = \Config\Database::connect();
        
        $token = $db->table('usuario_calendar_tokens')
            ->where('usuario_id', $usuarioId)
            ->where('provider', $this->provider)
            ->get()
            ->getRow();
        
        if ($token) {
            log_message('info', 'Token encontrado para usuario ID: ' . $usuarioId . ', expira: ' . $token->expires_at);
            log_message('info', 'Token tiene access_token: ' . (!empty($token->access_token) ? 'Sí (' . substr($token->access_token, 0, 20) . '...)' : 'NO'));
            log_message('info', 'Token tiene refresh_token: ' . (!empty($token->refresh_token) ? 'Sí' : 'NO'));
            
            // Si el token está expirado, intentar refrescarlo
            $expiresAt = strtotime($token->expires_at);
            $now = time();
            log_message('info', 'Token expira en: ' . date('Y-m-d H:i:s', $expiresAt) . ', ahora: ' . date('Y-m-d H:i:s', $now) . ', diferencia: ' . ($expiresAt - $now) . ' segundos');
            
            if ($expiresAt < $now) {
                log_message('info', 'Token expirado, intentando refrescar...');
                if (!empty($token->refresh_token)) {
                    $this->refreshToken($usuarioId, $token->refresh_token);
                } else {
                    throw new \Exception('Token expirado y no hay refresh_token. El usuario debe re-autorizar.');
                }
            } else {
                $this->accessToken = $token->access_token;
                log_message('info', 'Token cargado correctamente (válido hasta: ' . $token->expires_at . ')');
                log_message('info', 'Access token establecido: ' . (!empty($this->accessToken) ? 'Sí' : 'NO'));
            }
        } else {
            log_message('warning', 'No se encontró token para usuario ID: ' . $usuarioId . ', provider: ' . $this->provider);
        }
    }

    /**
     * Verificar y refrescar token si es necesario
     */
    protected function verificarYRefrescarToken($usuarioId)
    {
        if (!$usuarioId) {
            return;
        }
        
        $db = \Config\Database::connect();
        $token = $db->table('usuario_calendar_tokens')
            ->where('usuario_id', $usuarioId)
            ->where('provider', $this->provider)
            ->get()
            ->getRow();
        
        if ($token && strtotime($token->expires_at) < time()) {
            log_message('info', 'Token expirado detectado, refrescando...');
            if (!empty($token->refresh_token)) {
                $this->refreshToken($usuarioId, $token->refresh_token);
            } else {
                throw new \Exception('Token expirado y no hay refresh_token. El usuario debe re-autorizar.');
            }
        }
    }

    /**
     * Verificar y renovar token automáticamente si está próximo a expirar
     * Este método se puede llamar periódicamente mientras el usuario está activo
     * 
     * @param int $usuarioId ID del usuario
     * @param int $minutosAntes Minutos antes de la expiración para renovar (default: 60)
     * @return array Resultado de la verificación
     */
    public function verificarYRenovarTokenAutomatico($usuarioId, $minutosAntes = 60)
    {
        if (!$usuarioId) {
            return [
                'success' => false,
                'message' => 'Usuario no especificado',
                'renovado' => false
            ];
        }

        try {
            $db = \Config\Database::connect();
            $token = $db->table('usuario_calendar_tokens')
                ->where('usuario_id', $usuarioId)
                ->where('provider', $this->provider)
                ->get()
                ->getRow();

            if (!$token) {
                return [
                    'success' => true,
                    'message' => 'No hay token configurado',
                    'renovado' => false,
                    'tiene_token' => false
                ];
            }

            $expiresAt = strtotime($token->expires_at);
            $now = time();
            $segundosRestantes = $expiresAt - $now;
            $minutosRestantes = floor($segundosRestantes / 60);

            log_message('info', 'Verificando token para usuario ID: ' . $usuarioId . ', minutos restantes: ' . $minutosRestantes);

            // Si el token ya expiró, renovarlo inmediatamente
            if ($segundosRestantes <= 0) {
                log_message('info', 'Token expirado, renovando inmediatamente...');
                if (!empty($token->refresh_token)) {
                    $this->refreshToken($usuarioId, $token->refresh_token);
                    return [
                        'success' => true,
                        'message' => 'Token renovado exitosamente (estaba expirado)',
                        'renovado' => true,
                        'tiene_token' => true,
                        'minutos_restantes' => 0
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Token expirado y no hay refresh_token. El usuario debe re-autorizar.',
                        'renovado' => false,
                        'tiene_token' => true,
                        'necesita_autorizar' => true
                    ];
                }
            }

            // Si el token está próximo a expirar (menos de X minutos), renovarlo preventivamente
            if ($minutosRestantes <= $minutosAntes) {
                log_message('info', 'Token próximo a expirar (' . $minutosRestantes . ' minutos), renovando preventivamente...');
                if (!empty($token->refresh_token)) {
                    $this->refreshToken($usuarioId, $token->refresh_token);
                    return [
                        'success' => true,
                        'message' => 'Token renovado preventivamente',
                        'renovado' => true,
                        'tiene_token' => true,
                        'minutos_restantes_antes' => $minutosRestantes
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Token próximo a expirar pero no hay refresh_token. El usuario debe re-autorizar.',
                        'renovado' => false,
                        'tiene_token' => true,
                        'necesita_autorizar' => true,
                        'minutos_restantes' => $minutosRestantes
                    ];
                }
            }

            // Token aún válido, no necesita renovación
            return [
                'success' => true,
                'message' => 'Token válido, no requiere renovación',
                'renovado' => false,
                'tiene_token' => true,
                'minutos_restantes' => $minutosRestantes
            ];

        } catch (\Exception $e) {
            log_message('error', 'Error al verificar/renovar token: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al verificar token: ' . $e->getMessage(),
                'renovado' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Guardar token de acceso del usuario
     */
    protected function saveUserToken($usuarioId, $tokenData)
    {
        $db = \Config\Database::connect();
        
        // Validar que tenemos los datos necesarios
        if (empty($tokenData['access_token'])) {
            throw new \Exception('No se recibió access_token de Google');
        }
        
        // Verificar si ya existe
        $existente = $db->table('usuario_calendar_tokens')
            ->where('usuario_id', $usuarioId)
            ->where('provider', $this->provider)
            ->get()
            ->getRow();
        
        // Preparar datos para guardar
        $data = [
            'usuario_id' => $usuarioId,
            'provider' => $this->provider,
            'access_token' => $tokenData['access_token'],
            'expires_at' => date('Y-m-d H:i:s', time() + ($tokenData['expires_in'] ?? 3600)),
            'token_type' => $tokenData['token_type'] ?? 'Bearer'
        ];
        
        // Manejar refresh_token: si no viene en la respuesta y existe uno previo, preservarlo
        if (isset($tokenData['refresh_token']) && !empty($tokenData['refresh_token'])) {
            $data['refresh_token'] = $tokenData['refresh_token'];
            log_message('info', 'Nuevo refresh_token recibido, se actualizará');
        } elseif ($existente && !empty($existente->refresh_token)) {
            // Preservar el refresh_token existente si no se recibió uno nuevo
            $data['refresh_token'] = $existente->refresh_token;
            log_message('info', 'Preservando refresh_token existente (no se recibió uno nuevo)');
        } else {
            $data['refresh_token'] = null;
            log_message('warning', 'No hay refresh_token para preservar');
        }
        
        log_message('info', 'Guardando token para usuario ID: ' . $usuarioId . ', provider: ' . $this->provider);
        log_message('info', 'Token expira en: ' . $data['expires_at']);
        
        if ($existente) {
            $updated = $db->table('usuario_calendar_tokens')
                ->where('id', $existente->id)
                ->update($data);
            log_message('info', 'Token actualizado: ' . ($updated ? 'sí' : 'no'));
        } else {
            $inserted = $db->table('usuario_calendar_tokens')->insert($data);
            log_message('info', 'Token insertado: ' . ($inserted ? 'sí' : 'no') . ', ID: ' . $db->insertID());
        }
        
        // Verificar que se guardó
        $verificado = $db->table('usuario_calendar_tokens')
            ->where('usuario_id', $usuarioId)
            ->where('provider', $this->provider)
            ->get()
            ->getRow();
        
        if (!$verificado) {
            throw new \Exception('Error: El token no se guardó correctamente en la base de datos');
        }
        
        log_message('info', 'Token verificado y guardado correctamente para usuario ID: ' . $usuarioId);
        log_message('info', 'Token guardado en BD - access_token: ' . (!empty($verificado->access_token) ? 'SÍ' : 'NO'));
        log_message('info', 'Token guardado en BD - refresh_token: ' . (!empty($verificado->refresh_token) ? 'SÍ (' . substr($verificado->refresh_token, 0, 20) . '...)' : 'NULL'));
        log_message('info', 'Token guardado en BD - expires_at: ' . $verificado->expires_at);
    }

    /**
     * Refrescar token de acceso
     */
    protected function refreshToken($usuarioId, $refreshToken)
    {
        $config = $this->config[$this->provider];
        
        log_message('info', 'Refrescando token para usuario ID: ' . $usuarioId . ', provider: ' . $this->provider);
        
        $data = [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ];
        
        // Configurar SSL para refresh token también
        $requestOptions = [
            'form_params' => $data,
            'http_errors' => false // Para capturar el body del error
        ];
        
        $disableSsl = (ENVIRONMENT === 'development' && env('GOOGLE_DISABLE_SSL_VERIFY', false));
        if ($disableSsl) {
            $requestOptions['verify'] = false;
        } else {
            $caBundlePath = $this->findCaBundle();
            if ($caBundlePath) {
                $requestOptions['verify'] = $caBundlePath;
            } elseif (ENVIRONMENT === 'development') {
                // Si no hay CA bundle y estamos en desarrollo, deshabilitar SSL
                $requestOptions['verify'] = false;
            }
        }
        
        try {
            $response = $this->httpClient->request('POST', $config['token_url'], $requestOptions);
            $statusCode = $response->getStatusCode();
            
            // En CodeIgniter 4, getBody() puede devolver un string directamente o un objeto stream
            $bodyObj = $response->getBody();
            if (is_string($bodyObj)) {
                $body = $bodyObj;
            } else {
                $body = $bodyObj->getContents();
            }
            
            $tokenData = json_decode($body, true);
            
            log_message('info', 'Respuesta de refresh token - Status: ' . $statusCode);
            log_message('info', 'Body de respuesta: ' . substr($body, 0, 200));
            
            if ($statusCode !== 200) {
                $errorMsg = $tokenData['error'] ?? 'Error desconocido';
                $errorDesc = $tokenData['error_description'] ?? 'Sin descripción';
                log_message('error', 'Error al refrescar token: ' . $errorMsg . ' - ' . $errorDesc);
                throw new \Exception('Error al refrescar token: ' . $errorMsg . ' - ' . $errorDesc);
            }
            
            if (isset($tokenData['access_token'])) {
                log_message('info', 'Token refrescado exitosamente. Nuevo access_token recibido.');
                
                // Obtener el refresh_token existente antes de guardar (por si Google no devuelve uno nuevo)
                $db = \Config\Database::connect();
                $tokenExistente = $db->table('usuario_calendar_tokens')
                    ->where('usuario_id', $usuarioId)
                    ->where('provider', $this->provider)
                    ->get()
                    ->getRow();
                
                // Si Google no devuelve un nuevo refresh_token, preservar el existente
                if (empty($tokenData['refresh_token']) && $tokenExistente && !empty($tokenExistente->refresh_token)) {
                    log_message('info', 'Google no devolvió nuevo refresh_token, preservando el existente');
                    $tokenData['refresh_token'] = $tokenExistente->refresh_token;
                }
                
                $this->saveUserToken($usuarioId, $tokenData);
                $this->accessToken = $tokenData['access_token'];
                log_message('info', 'Token guardado y accessToken actualizado en memoria');
            } else {
                log_message('error', 'No se recibió access_token en la respuesta de refresh');
                throw new \Exception('No se recibió access_token al refrescar el token');
            }
        } catch (\Exception $e) {
            log_message('error', 'Excepción al refrescar token: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar evento en Google Calendar
     */
    protected function actualizarEventoGoogle($eventId, $citaData)
    {
        $config = $this->config['google'];
        $fechaISO = $this->convertirFechaAISO($citaData['fecha'], $citaData['hora_inicio'], $citaData['hora_fin']);
        
        $evento = [
            'summary' => 'Consulta: ' . ($citaData['nombre_paciente'] ?? 'Paciente'),
            'description' => $this->generarDescripcionEvento($citaData),
            'start' => [
                'dateTime' => $fechaISO['inicio'],
                'timeZone' => 'America/Santiago'
            ],
            'end' => [
                'dateTime' => $fechaISO['fin'],
                'timeZone' => 'America/Santiago'
            ]
        ];
        
        $requestOptions = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ],
            'json' => $evento
        ];
        
        // Agregar opciones SSL
        $this->agregarOpcionesSSL($requestOptions);
        
        $response = $this->httpClient->request('PUT', $config['api_url'] . '/calendars/primary/events/' . $eventId, $requestOptions);
        
        return json_decode($response->getBody(), true);
    }

    /**
     * Actualizar evento en Outlook Calendar
     */
    protected function actualizarEventoOutlook($eventId, $citaData)
    {
        $config = $this->config['outlook'];
        $fechaISO = $this->convertirFechaAISO($citaData['fecha'], $citaData['hora_inicio'], $citaData['hora_fin']);
        
        $evento = [
            'subject' => 'Consulta: ' . ($citaData['nombre_paciente'] ?? 'Paciente'),
            'start' => [
                'dateTime' => $fechaISO['inicio'],
                'timeZone' => 'America/Santiago'
            ],
            'end' => [
                'dateTime' => $fechaISO['fin'],
                'timeZone' => 'America/Santiago'
            ]
        ];
        
        $requestOptions = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ],
            'json' => $evento
        ];
        
        // Agregar opciones SSL
        $this->agregarOpcionesSSL($requestOptions);
        
        $response = $this->httpClient->request('PATCH', $config['api_url'] . '/me/events/' . $eventId, $requestOptions);
        
        return json_decode($response->getBody(), true);
    }

    /**
     * Extraer el enlace de Google Meet de la respuesta del evento
     */
    protected function extraerMeetLink($eventoData)
    {
        if (isset($eventoData['conferenceData']['entryPoints'])) {
            foreach ($eventoData['conferenceData']['entryPoints'] as $entryPoint) {
                if (isset($entryPoint['entryPointType']) && $entryPoint['entryPointType'] === 'video') {
                    return $entryPoint['uri'] ?? null;
                }
            }
        }
        return null;
    }

    /**
     * Agregar opciones SSL a las opciones de request
     */
    protected function agregarOpcionesSSL(&$requestOptions)
    {
        $disableSsl = (ENVIRONMENT === 'development' && env('GOOGLE_DISABLE_SSL_VERIFY', false));
        
        if ($disableSsl) {
            $requestOptions['verify'] = false;
        } else {
            $caBundlePath = $this->findCaBundle();
            if ($caBundlePath) {
                $requestOptions['verify'] = $caBundlePath;
            } elseif (ENVIRONMENT === 'development') {
                // Si no hay CA bundle y estamos en desarrollo, deshabilitar SSL
                $requestOptions['verify'] = false;
            }
        }
    }

    /**
     * Buscar bundle de certificados CA
     */
    protected function findCaBundle()
    {
        $caBundlePaths = [
            'C:/wamp64/bin/php/php8.1.0/extras/ssl/cacert.pem', // WAMP común
            'C:/wamp64/bin/php/php8.2.0/extras/ssl/cacert.pem', // WAMP PHP 8.2
            'C:/wamp64/bin/php/php8.3.0/extras/ssl/cacert.pem', // WAMP PHP 8.3
            'C:/xampp/apache/bin/curl-ca-bundle.crt', // XAMPP común
            __DIR__ . '/../../vendor/twilio/sdk/src/Twilio/cacert.pem', // Bundle incluido en Twilio SDK (si está instalado)
            getcwd() . '/vendor/twilio/sdk/src/Twilio/cacert.pem', // Path relativo
        ];
        
        foreach ($caBundlePaths as $caPath) {
            if (file_exists($caPath)) {
                return $caPath;
            }
        }
        
        return null;
    }
}
