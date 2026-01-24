<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class MercadoPago extends BaseConfig
{
    /**
     * Access Token de Mercado Pago
     * Obtener desde: https://www.mercadopago.com.mx/developers/panel/credentials
     */
    public string $accessToken = '';

    /**
     * Public Key de Mercado Pago
     * Obtener desde: https://www.mercadopago.com.mx/developers/panel/credentials
     */
    public string $publicKey = '';

    /**
     * Modo: 'sandbox' para pruebas, 'production' para producción
     */
    public string $mode = 'sandbox';

    /**
     * URL base para webhooks
     * Se usará para construir la URL completa del webhook
     */
    public string $webhookBaseUrl = '';

    /**
     * URL de éxito después del pago
     */
    public string $successUrl = '/dashboard/pago/success';

    /**
     * URL de fallo después del pago
     */
    public string $failureUrl = '/dashboard/pago/failure';

    /**
     * URL de pendiente después del pago
     */
    public string $pendingUrl = '/dashboard/pago/pending';

    /**
     * Moneda por defecto
     */
    public string $defaultCurrency = 'CLP';

    /**
     * Obtener Access Token desde .env o variable de entorno
     */
    public function __construct()
    {
        parent::__construct();
        
        // Intentar obtener desde .env
        $this->accessToken = $_ENV['MERCADOPAGO_ACCESS_TOKEN'] ?? $this->accessToken;
        $this->publicKey = $_ENV['MERCADOPAGO_PUBLIC_KEY'] ?? $this->publicKey;
        $this->mode = $_ENV['MERCADOPAGO_MODE'] ?? $this->mode;
        $this->webhookBaseUrl = $_ENV['MERCADOPAGO_WEBHOOK_BASE_URL'] ?? $this->webhookBaseUrl;
    }
}
