<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\WhatsAppService;
use Config\Services;

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
        $mode = $this->request->getGet('hub_mode');
        $token = $this->request->getGet('hub_verify_token');
        $challenge = $this->request->getGet('hub_challenge');

        $verifyToken = env('WHATSAPP_VERIFY_TOKEN', 'nextline_verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            log_message('info', 'WhatsApp webhook verificado correctamente');
            return $this->response->setBody($challenge);
        }

        log_message('warning', 'WhatsApp webhook verificación fallida');
        return $this->response->setStatusCode(403);
    }

    /**
     * Webhook para recibir mensajes (WhatsApp Business API y Twilio)
     * POST /whatsapp/webhook
     */
    public function webhook()
    {
        $provider = env('WHATSAPP_PROVIDER', 'twilio');
        
        if ($provider === 'twilio') {
            return $this->procesarWebhookTwilio();
        } elseif ($provider === 'whatsapp_business') {
            return $this->procesarWebhookWhatsAppBusiness();
        }

        return $this->response->setStatusCode(400)->setJSON(['error' => 'Provider no configurado']);
    }

    /**
     * Procesar webhook de Twilio
     */
    protected function procesarWebhookTwilio()
    {
        $post = $this->request->getPost();
        
        // Twilio envía los datos como POST form
        $numeroOrigen = $post['From'] ?? null;
        $numeroDestino = $post['To'] ?? null;
        $mensajeTexto = $post['Body'] ?? null;
        $mensajeId = $post['MessageSid'] ?? null;
        $status = $post['MessageStatus'] ?? null;
        
        // Si es actualización de estado
        if ($status && $mensajeId) {
            return $this->actualizarEstadoMensaje($mensajeId, $status);
        }
        
        // Si es mensaje entrante
        if ($numeroOrigen && $mensajeTexto) {
            // Limpiar formato de Twilio (whatsapp:+56912345678 -> +56912345678)
            $numeroOrigen = str_replace('whatsapp:', '', $numeroOrigen);
            
            log_message('info', 'Mensaje recibido de Twilio: ' . $numeroOrigen . ' - ' . $mensajeTexto);
            
            $resultado = $this->whatsappService->procesarMensajeEntrante(
                $numeroOrigen,
                $mensajeTexto,
                $mensajeId
            );
            
            return $this->response->setJSON($resultado);
        }
        
        return $this->response->setStatusCode(400)->setJSON(['error' => 'Datos incompletos']);
    }

    /**
     * Procesar webhook de WhatsApp Business API
     */
    protected function procesarWebhookWhatsAppBusiness()
    {
        $json = $this->request->getJSON(true);
        
        if (!isset($json['entry'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Formato inválido']);
        }
        
        foreach ($json['entry'] as $entry) {
            if (!isset($entry['changes'])) {
                continue;
            }
            
            foreach ($entry['changes'] as $change) {
                if ($change['field'] !== 'messages') {
                    continue;
                }
                
                $value = $change['value'] ?? [];
                
                // Procesar mensajes entrantes
                if (isset($value['messages'])) {
                    foreach ($value['messages'] as $message) {
                        $this->procesarMensajeWhatsAppBusiness($message, $value);
                    }
                }
                
                // Procesar actualizaciones de estado
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
        
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Procesar mensaje de WhatsApp Business API
     */
    protected function procesarMensajeWhatsAppBusiness($message, $value)
    {
        $tipo = $message['type'] ?? 'unknown';
        $mensajeId = $message['id'] ?? null;
        $numeroOrigen = $message['from'] ?? null;
        
        // Solo procesar mensajes de texto
        if ($tipo !== 'text') {
            log_message('info', 'Mensaje no texto recibido: ' . $tipo);
            return;
        }
        
        $mensajeTexto = $message['text']['body'] ?? '';
        
        log_message('info', 'Mensaje recibido de WhatsApp Business: ' . $numeroOrigen . ' - ' . $mensajeTexto);
        
        // Formatear número (agregar + si no lo tiene)
        if (substr($numeroOrigen, 0, 1) !== '+') {
            $numeroOrigen = '+' . $numeroOrigen;
        }
        
        $this->whatsappService->procesarMensajeEntrante(
            $numeroOrigen,
            $mensajeTexto,
            $mensajeId
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
            'failed' => 'error'
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
        
        $whatsappModel = new \App\Models\WhatsAppMensaje();
        
        $mensaje = $whatsappModel->where('mensaje_id_api', $mensajeId)->first();
        
        if ($mensaje) {
            $whatsappModel->actualizarEstado($mensaje->id, $estado, date('Y-m-d H:i:s'));
        }
        
        return $this->response->setJSON(['success' => true]);
    }
}
