<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\WhatsAppService;
use App\Models\WhatsAppMensaje;

/**
 * Controlador para recibir webhooks de WhatsApp
 * Maneja mensajes entrantes y actualizaciones de estado
 */
class WhatsAppWebhookController extends BaseController
{
    protected $whatsappService;

    public function __construct()
    {
        $this->whatsappService = new WhatsAppService();
    }

    /**
     * Webhook para verificación (WhatsApp Business API)
     * GET /whatsapp/webhook
     */
    public function verify()
    {
        $mode = $this->request->getGet('hub_mode') ?? $this->request->getGet('hub.mode');
        $token = $this->request->getGet('hub_verify_token') ?? $this->request->getGet('hub.verify_token');
        $challenge = $this->request->getGet('hub_challenge') ?? $this->request->getGet('hub.challenge');

        $verifyToken = $this->resolverVerifyToken();

        log_message('info', 'WhatsApp webhook verify: mode=' . ($mode ?? 'null') . ' token_match=' . ($token === $verifyToken ? 'yes' : 'no'));

        if ($mode === 'subscribe' && $token === $verifyToken && $challenge !== null && $challenge !== '') {
            return $this->response
                ->setStatusCode(200)
                ->setContentType('text/plain')
                ->setBody((string) $challenge);
        }

        log_message('warning', 'WhatsApp webhook verificación fallida. Esperado token distinto o challenge vacío.');

        return $this->response->setStatusCode(403)->setBody('Forbidden');
    }

    /**
     * Webhook para recibir mensajes (WhatsApp Business API)
     * POST /whatsapp/webhook
     */
    public function webhook()
    {
        return $this->procesarWebhookWhatsAppBusiness();
    }

    /**
     * Token de verificación: .env, luego empresa_configuracion, luego default.
     */
    protected function resolverVerifyToken(): string
    {
        $env = env('WHATSAPP_VERIFY_TOKEN');
        if ($env !== null && $env !== false && trim((string) $env) !== '') {
            return trim((string) $env);
        }

        $db = \Config\Database::connect();
        $row = $db->table('empresa_configuracion')
            ->select('whatsapp_verify_token')
            ->where('whatsapp_verify_token IS NOT NULL', null, false)
            ->where('whatsapp_verify_token !=', '')
            ->orderBy('id', 'ASC')
            ->limit(1)
            ->get()
            ->getRow();

        if ($row && trim((string) $row->whatsapp_verify_token) !== '') {
            return trim((string) $row->whatsapp_verify_token);
        }

        return 'nextline_verify_token';
    }

    /**
     * Procesar webhook de WhatsApp Business API
     */
    protected function procesarWebhookWhatsAppBusiness()
    {
        $json = $this->request->getJSON(true);

        if (!is_array($json) || !isset($json['entry'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Formato inválido']);
        }

        foreach ($json['entry'] as $entry) {
            if (!isset($entry['changes'])) {
                continue;
            }

            foreach ($entry['changes'] as $change) {
                if (($change['field'] ?? '') !== 'messages') {
                    continue;
                }

                $value = $change['value'] ?? [];

                if (isset($value['messages'])) {
                    foreach ($value['messages'] as $message) {
                        $this->procesarMensajeWhatsAppBusiness($message, $value);
                    }
                }

                if (isset($value['statuses'])) {
                    foreach ($value['statuses'] as $status) {
                        $this->actualizarEstadoMensaje(
                            $status['id'] ?? null,
                            $this->mapearEstadoWhatsAppBusiness($status['status'] ?? 'unknown')
                        );
                    }
                }
            }
        }

        return $this->response->setStatusCode(200)->setJSON(['success' => true]);
    }

    /**
     * Procesar mensaje de WhatsApp Business API
     */
    protected function procesarMensajeWhatsAppBusiness($message, $value)
    {
        $tipo = $message['type'] ?? 'unknown';
        $mensajeId = $message['id'] ?? null;
        $numeroOrigen = $message['from'] ?? null;

        if ($tipo !== 'text') {
            log_message('info', 'Mensaje no texto recibido: ' . $tipo);

            return;
        }

        $mensajeTexto = $message['text']['body'] ?? '';

        log_message('info', 'Mensaje recibido de WhatsApp Business: ' . $numeroOrigen . ' - ' . $mensajeTexto);

        $soloDigitos = preg_replace('/\D+/', '', (string) $numeroOrigen);
        if ($soloDigitos !== '') {
            $numeroOrigen = '+' . $soloDigitos;
        }

        $metadata = $value['metadata'] ?? [];
        $lineaEmpresa = $metadata['display_phone_number'] ?? null;

        $this->whatsappService->procesarMensajeEntrante(
            $numeroOrigen,
            $mensajeTexto,
            $mensajeId,
            $lineaEmpresa
        );
    }

    /**
     * Mapear estado de WhatsApp Business API a estado interno
     */
    protected function mapearEstadoWhatsAppBusiness($status)
    {
        $map = [
            'sent' => 'enviado',
            'delivered' => 'entregado',
            'read' => 'leido',
            'failed' => 'error',
        ];

        return $map[$status] ?? 'pendiente';
    }

    /**
     * Actualizar estado de mensaje en BD
     */
    protected function actualizarEstadoMensaje($mensajeId, $estado)
    {
        if (!$mensajeId) {
            return;
        }

        $whatsappModel = new WhatsAppMensaje();

        $mensaje = $whatsappModel->where('mensaje_id_api', $mensajeId)->first();

        if ($mensaje) {
            $whatsappModel->actualizarEstado($mensaje->id, $estado, date('Y-m-d H:i:s'));
        }
    }
}
