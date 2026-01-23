<?php

namespace App\Services;

use Config\MercadoPago;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Resources\Preference;
use MercadoPago\Resources\Payment;
use Exception;

/**
 * Servicio para manejar integración con Mercado Pago
 * Compatible con SDK 3.8+
 */
class MercadoPagoService
{
    protected $accessToken;
    protected $publicKey;
    protected $mode;
    protected $webhookBaseUrl;
    protected $preferenceClient;
    protected $paymentClient;

    /**
     * Constructor - Recibe credenciales de la empresa
     * 
     * @param string $accessToken Access Token de Mercado Pago
     * @param string $publicKey Public Key de Mercado Pago (opcional)
     * @param string $mode Modo: 'sandbox' o 'production'
     * @param string $webhookBaseUrl URL base para webhooks
     */
    public function __construct($accessToken = null, $publicKey = null, $mode = 'sandbox', $webhookBaseUrl = null)
    {
        // Si no se proporcionan credenciales, usar configuración global (fallback)
        if (empty($accessToken)) {
            $config = new MercadoPago();
            $this->accessToken = $config->accessToken;
            $this->publicKey = $config->publicKey;
            $this->mode = $config->mode;
            $this->webhookBaseUrl = $config->webhookBaseUrl ?: base_url();
        } else {
            $this->accessToken = $accessToken;
            $this->publicKey = $publicKey;
            // Normalizar el modo a minúsculas para comparaciones consistentes
            $this->mode = strtolower(trim($mode ?? 'sandbox'));
            $this->webhookBaseUrl = $webhookBaseUrl ?: base_url();
        }
        
        // Log del modo configurado (info, no error)
        log_message('info', 'MercadoPagoService inicializado - Modo: ' . $this->mode);
        
        // Inicializar SDK de Mercado Pago (SDK 3.8+)
        MercadoPagoConfig::setAccessToken($this->accessToken);
        
        // Configurar modo (sandbox o production)
        if ($this->mode === 'sandbox') {
            MercadoPagoConfig::setIntegratorId('dev_24c65fb163bf11ea96500242ac130004');
        }
        
        // Configurar entorno de ejecución para SSL
        // En desarrollo local, usar LOCAL para deshabilitar verificación SSL
        // En producción, usar SERVER para habilitar verificación SSL
        if (ENVIRONMENT === 'development') {
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
        } else {
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::SERVER);
        }
        
        // Inicializar clientes
        $this->preferenceClient = new PreferenceClient();
        $this->paymentClient = new PaymentClient();
    }

    /**
     * Crear preferencia de pago (botón de pago)
     * 
     * @param array $data Datos del pago:
     *   - title: Título del pago
     *   - description: Descripción
     *   - quantity: Cantidad (default: 1)
     *   - unit_price: Precio unitario
     *   - payer_email: Email del pagador
     *   - payer_name: Nombre del pagador
     *   - payer_surname: Apellido del pagador
     *   - external_reference: Referencia externa (ID del pago en nuestra BD)
     *   - currency: Moneda (default: CLP)
     *   - statement_descriptor: Descriptor del estado de cuenta
     * 
     * @return array ['preference_id' => string, 'init_point' => string, 'sandbox_init_point' => string]
     * @throws Exception
     */
    public function crearPreferencia(array $data): array
    {
        try {
            // Validar datos requeridos
            if (empty($data['title']) || empty($data['unit_price'])) {
                throw new Exception('Faltan datos requeridos para crear la preferencia (title, unit_price)');
            }
            
            // En sandbox, si no hay payer_email o es un email real, usar email de prueba estándar
            $payerEmail = $data['payer_email'] ?? null;
            $modoActual = strtolower(trim($this->mode ?? 'sandbox'));
            
            log_message('error', 'MercadoPagoService - Modo detectado: ' . $modoActual);
            log_message('error', 'MercadoPagoService - Email recibido: ' . ($payerEmail ?? 'NULL'));
            
            if ($modoActual === 'sandbox') {
                if (empty($payerEmail) || !$this->esEmailPrueba($payerEmail)) {
                    // En sandbox, usar un email de prueba estándar de Mercado Pago
                    // IMPORTANTE: En sandbox, el email debe ser de un usuario de prueba válido
                    // Usamos un formato estándar que Mercado Pago acepta
                    $payerEmail = 'test_user_' . rand(100000000, 999999999) . '@testuser.com';
                    log_message('error', 'Modo sandbox: Email original no es de prueba. Usando email de prueba automático: ' . $payerEmail);
                } else {
                    log_message('error', 'Modo sandbox: Email ya es de prueba, usando: ' . $payerEmail);
                }
            } else {
                // En producción, el email es obligatorio
                if (empty($payerEmail)) {
                    throw new Exception('El email del pagador es obligatorio en modo producción');
                }
                log_message('error', 'Modo producción: Usando email real: ' . $payerEmail);
            }
            
            log_message('error', 'MercadoPagoService - Email final que se usará: ' . $payerEmail);

            $config = new MercadoPago();
            
            // Obtener base URL - asegurar que no esté vacía
            $baseUrl = $this->webhookBaseUrl ?: base_url();
            if (empty($baseUrl)) {
                throw new Exception('La URL base no puede estar vacía. Configure webhookBaseUrl o app.baseURL');
            }
            
            // Limpiar base URL (remover trailing slash)
            $baseUrl = rtrim($baseUrl, '/');
            
            // Construir URLs de retorno (sin placeholders - Mercado Pago agregará los parámetros automáticamente)
            $successPath = $config->successUrl ?? '/dashboard/pago/success';
            $failurePath = $config->failureUrl ?? '/dashboard/pago/failure';
            $pendingPath = $config->pendingUrl ?? '/dashboard/pago/pending';
            
            // Asegurar que los paths comiencen con /
            if (!str_starts_with($successPath, '/')) {
                $successPath = '/' . $successPath;
            }
            if (!str_starts_with($failurePath, '/')) {
                $failurePath = '/' . $failurePath;
            }
            if (!str_starts_with($pendingPath, '/')) {
                $pendingPath = '/' . $pendingPath;
            }
            
            $successUrl = $baseUrl . $successPath;
            $failureUrl = $baseUrl . $failurePath;
            $pendingUrl = $baseUrl . $pendingPath;
            
            // Validar que las URLs no estén vacías
            if (empty($successUrl) || empty($failureUrl) || empty($pendingUrl)) {
                throw new Exception('Las URLs de retorno no pueden estar vacías. Success: ' . $successUrl . ', Failure: ' . $failureUrl . ', Pending: ' . $pendingUrl);
            }
            
            // Asegurar que las URLs comiencen con http:// o https://
            if (!preg_match('/^https?:\/\//', $successUrl)) {
                throw new Exception('URL de éxito debe ser absoluta (comenzar con http:// o https://). Actual: ' . $successUrl);
            }
            if (!preg_match('/^https?:\/\//', $failureUrl)) {
                throw new Exception('URL de fallo debe ser absoluta (comenzar con http:// o https://). Actual: ' . $failureUrl);
            }
            if (!preg_match('/^https?:\/\//', $pendingUrl)) {
                throw new Exception('URL de pendiente debe ser absoluta (comenzar con http:// o https://). Actual: ' . $pendingUrl);
            }
            
            // Asegurar que todas las URLs sean strings válidos (no null, no vacíos)
            $successUrl = (string)$successUrl;
            $failureUrl = (string)$failureUrl;
            $pendingUrl = (string)$pendingUrl;
            
            // Verificación final antes de construir back_urls
            if (empty($successUrl) || $successUrl === '') {
                throw new Exception('URL de éxito está vacía o es inválida');
            }
            if (empty($failureUrl) || $failureUrl === '') {
                throw new Exception('URL de fallo está vacía o es inválida');
            }
            if (empty($pendingUrl) || $pendingUrl === '') {
                throw new Exception('URL de pendiente está vacía o es inválida');
            }
            
            // Construir back_urls - IMPORTANTE: debe estar antes de auto_return
            // Asegurar que todas las claves y valores sean strings válidos
            $backUrls = [
                'success' => trim($successUrl),
                'failure' => trim($failureUrl),
                'pending' => trim($pendingUrl)
            ];
            
            // Log para debugging antes de crear el request (usar 'error' para asegurar que se muestre)
            log_message('error', '=== Creando Preferencia Mercado Pago ===');
            log_message('error', 'Base URL: ' . $baseUrl);
            log_message('error', 'Success URL: ' . $successUrl . ' (tipo: ' . gettype($successUrl) . ', longitud: ' . strlen($successUrl) . ')');
            log_message('error', 'Failure URL: ' . $failureUrl . ' (tipo: ' . gettype($failureUrl) . ', longitud: ' . strlen($failureUrl) . ')');
            log_message('error', 'Pending URL: ' . $pendingUrl . ' (tipo: ' . gettype($pendingUrl) . ', longitud: ' . strlen($pendingUrl) . ')');
            log_message('error', 'Back URLs completo: ' . json_encode($backUrls, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            
            // Verificar si estamos usando localhost - Mercado Pago no acepta localhost con auto_return
            $isLocalhost = (strpos($successUrl, 'localhost') !== false || 
                           strpos($successUrl, '127.0.0.1') !== false ||
                           strpos($successUrl, '::1') !== false);
            
            // Construir request para la preferencia
            $request = [
                'items' => [
                    [
                        'title' => $data['title'],
                        'description' => $data['description'] ?? '',
                        'quantity' => intval($data['quantity'] ?? 1),
                        'unit_price' => floatval($data['unit_price']),
                        'currency_id' => $data['currency'] ?? $config->defaultCurrency ?? 'CLP'
                    ]
                ],
                'back_urls' => $backUrls
            ];
            
            // Agregar información del pagador solo si tenemos email válido
            // En sandbox, siempre incluimos el payer con email de prueba
            if (!empty($payerEmail)) {
                $request['payer'] = [
                    'email' => $payerEmail
                ];
                
                // Agregar nombre y apellido solo si están disponibles
                if (!empty($data['payer_name'])) {
                    $request['payer']['name'] = $data['payer_name'];
                }
                if (!empty($data['payer_surname'])) {
                    $request['payer']['surname'] = $data['payer_surname'];
                }
                
                log_message('error', 'Payer agregado al request: ' . json_encode($request['payer']));
            } else {
                log_message('error', 'ADVERTENCIA: No se agregó payer al request (email vacío)');
            }
            
            // IMPORTANTE: Solo agregar auto_return si NO estamos usando localhost
            // Mercado Pago rechaza auto_return cuando las URLs son localhost
            if (!$isLocalhost) {
                $request['auto_return'] = 'approved';
                log_message('error', 'auto_return incluido (URLs públicas)');
            } else {
                log_message('error', 'auto_return OMITIDO (URLs localhost - Mercado Pago no lo acepta)');
            }
            
            $request['statement_descriptor'] = $data['statement_descriptor'] ?? 'NextLine Nutrición';
            
            // Agregar campos opcionales solo si no son null
            if (!empty($data['external_reference'])) {
                $request['external_reference'] = (string)$data['external_reference'];
            }
            
            $webhookUrl = $this->getWebhookUrl();
            if (!empty($webhookUrl)) {
                $request['notification_url'] = $webhookUrl;
            }
            
            // VERIFICACIÓN FINAL: Asegurar que back_urls.success esté definido y no vacío
            if (empty($request['back_urls']['success'])) {
                throw new Exception('back_urls.success no puede estar vacío. URL generada: ' . ($successUrl ?? 'NULL'));
            }
            
            // Si auto_return está presente, verificar que no sea localhost
            if (isset($request['auto_return']) && $isLocalhost) {
                log_message('error', 'ADVERTENCIA: auto_return fue removido porque se detectó localhost');
                unset($request['auto_return']);
            }
            
            // Log del request completo (sin datos sensibles) - usar 'error' para asegurar que se muestre
            $requestLog = $request;
            if (isset($requestLog['payer']['email'])) {
                $requestLog['payer']['email'] = '***';
            }
            log_message('error', 'Request completo: ' . json_encode($requestLog, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            log_message('error', 'Verificación final - back_urls.success: ' . ($request['back_urls']['success'] ?? 'NULL'));
            log_message('error', 'Verificación final - back_urls.failure: ' . ($request['back_urls']['failure'] ?? 'NULL'));
            log_message('error', 'Verificación final - back_urls.pending: ' . ($request['back_urls']['pending'] ?? 'NULL'));
            log_message('error', 'Tipo de back_urls: ' . gettype($request['back_urls']));
            log_message('error', 'back_urls es array?: ' . (is_array($request['back_urls']) ? 'SÍ' : 'NO'));
            
            // Crear preferencia usando el cliente
            $preference = $this->preferenceClient->create($request);

            if (!$preference || !$preference->id) {
                throw new Exception('Error al crear la preferencia: No se recibió ID de preferencia');
            }

            return [
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
                'preference' => $preference
            ];
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $errorMessage = $e->getMessage();
            $apiResponse = $e->getApiResponse();
            if ($apiResponse && $apiResponse->getContent()) {
                $errorData = $apiResponse->getContent();
                if (isset($errorData['message'])) {
                    $errorMessage = $errorData['message'];
                } elseif (isset($errorData['error'])) {
                    $errorMessage = $errorData['error'];
                } elseif (is_array($errorData) && !empty($errorData)) {
                    // Si el contenido es un array con información de error
                    $errorMessage = json_encode($errorData, JSON_UNESCAPED_UNICODE);
                }
            }
            log_message('error', 'Error al crear preferencia de Mercado Pago (API): ' . $errorMessage . ' | Status: ' . $apiResponse->getStatusCode());
            throw new Exception('Error al crear preferencia de pago: ' . $errorMessage);
        } catch (Exception $e) {
            log_message('error', 'Error al crear preferencia de Mercado Pago: ' . $e->getMessage());
            throw new Exception('Error al crear preferencia de pago: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si un email es de prueba de Mercado Pago
     * Los emails de prueba de Mercado Pago tienen el formato: test_user_XXXXXXXX@testuser.com
     * 
     * @param string $email
     * @return bool
     */
    private function esEmailPrueba(string $email): bool
    {
        if (empty($email)) {
            return false;
        }
        
        // Emails de prueba de Mercado Pago tienen el formato test_user_XXXXXXXX@testuser.com
        // También pueden ser emails que terminen en @testuser.com
        $esPrueba = (
            preg_match('/^test_user_\d+@testuser\.com$/', $email) ||
            strpos($email, '@testuser.com') !== false ||
            preg_match('/^test@.*$/', $email) || // También acepta emails que empiecen con "test@"
            preg_match('/^test_user@.*$/', $email) // test_user@ cualquier dominio
        );
        
        log_message('error', 'Verificando email de prueba: ' . $email . ' -> ' . ($esPrueba ? 'SÍ' : 'NO'));
        
        return $esPrueba;
    }

    /**
     * Obtener información de un pago
     * 
     * @param string $paymentId ID del pago en Mercado Pago
     * @return Payment|null
     */
    public function obtenerPago(string $paymentId): ?Payment
    {
        try {
            // Validar que el ID no sea un ID de prueba obvio
            if (empty($paymentId) || $paymentId === '123456' || !is_numeric($paymentId)) {
                log_message('debug', 'ID de pago inválido o de prueba: ' . $paymentId);
                return null;
            }
            
            $payment = $this->paymentClient->get(intval($paymentId));
            return $payment;
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            // Obtener código de estado HTTP si está disponible
            $statusCode = $e->getCode() ?? 0;
            $message = $e->getMessage();
            
            // Si es un 404 (no encontrado), solo loguear como debug, no como error
            if ($statusCode === 404 || strpos($message, '404') !== false || strpos($message, 'not found') !== false) {
                log_message('debug', 'Pago no encontrado en Mercado Pago (ID: ' . $paymentId . ') - Esto es normal para IDs de prueba');
            } else {
                log_message('error', 'Error al obtener pago de Mercado Pago (API): ' . $message . ' (Código: ' . $statusCode . ')');
            }
            return null;
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener pago de Mercado Pago: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener información de una preferencia
     * 
     * @param string $preferenceId ID de la preferencia
     * @return Preference|null
     */
    public function obtenerPreferencia(string $preferenceId): ?Preference
    {
        try {
            $preference = $this->preferenceClient->get($preferenceId);
            return $preference;
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            log_message('error', 'Error al obtener preferencia de Mercado Pago (API): ' . $e->getMessage());
            return null;
        } catch (Exception $e) {
            log_message('error', 'Error al obtener preferencia de Mercado Pago: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Procesar notificación de webhook
     * 
     * @param array $data Datos recibidos del webhook
     * @return array ['status' => string, 'payment_id' => string|null, 'payment' => Payment|null]
     */
    public function procesarWebhook(array $data): array
    {
        try {
            // Mercado Pago envía el tipo de notificación y el ID
            $type = $data['type'] ?? null;
            $dataId = $data['data']['id'] ?? null;

            if (!$type || !$dataId) {
                throw new Exception('Datos de webhook inválidos');
            }

            // Si es una notificación de pago
            if ($type === 'payment') {
                $payment = $this->obtenerPago($dataId);
                
                if (!$payment) {
                    throw new Exception('No se pudo obtener el pago');
                }

                return [
                    'status' => $payment->status,
                    'payment_id' => (string)$payment->id,
                    'external_reference' => $payment->external_reference,
                    'payment' => $payment
                ];
            }

            // Si es una notificación de preferencia (cuando se crea)
            if ($type === 'preference') {
                $preference = $this->obtenerPreferencia($dataId);
                
                return [
                    'status' => 'preference_created',
                    'preference_id' => $preference->id ?? null,
                    'preference' => $preference
                ];
            }

            return [
                'status' => 'unknown',
                'type' => $type
            ];
        } catch (Exception $e) {
            log_message('error', 'Error al procesar webhook de Mercado Pago: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener URL del webhook
     */
    protected function getWebhookUrl(): string
    {
        $baseUrl = rtrim($this->webhookBaseUrl ?: base_url(), '/');
        return $baseUrl . '/api/mercadopago/webhook';
    }

    /**
     * Mapear estado de Mercado Pago a estado interno
     * 
     * @param string $mpStatus Estado de Mercado Pago
     * @return string Estado interno
     */
    public function mapearEstado(string $mpStatus): string
    {
        $map = [
            'pending' => 'pendiente',
            'approved' => 'completado',
            'rejected' => 'fallido',
            'cancelled' => 'fallido',
            'refunded' => 'reembolsado',
            'charged_back' => 'reembolsado',
            'in_process' => 'procesando',
            'in_mediation' => 'procesando'
        ];

        return $map[$mpStatus] ?? 'pendiente';
    }
}
