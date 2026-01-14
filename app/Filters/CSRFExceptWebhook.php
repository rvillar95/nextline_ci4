<?php

namespace App\Filters;

use CodeIgniter\Filters\CSRF as BaseCSRF;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro CSRF que excluye el webhook de WhatsApp
 */
class CSRFExceptWebhook extends BaseCSRF
{
    /**
     * Rutas excluidas del CSRF
     */
    protected $except = [
        'whatsapp/webhook',
        'whatsapp/webhook/*',
        'cancelar-cita',
        'confirmar-cita',
        'inicio-sesion'
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        // Log para debugging de cancelar-cita y confirmar-cita
        if (strpos($path, 'cancelar-cita') !== false || strpos($path, 'confirmar-cita') !== false) {
            error_log('CSRFExceptWebhook: Procesando ruta: ' . $path);
            error_log('CSRFExceptWebhook: Método: ' . $request->getMethod());
            log_message('info', 'CSRFExceptWebhook: Procesando ruta: ' . $path);
            log_message('info', 'CSRFExceptWebhook: Método: ' . $request->getMethod());
        }

        // Verificar si la ruta está excluida
        foreach ($this->except as $except) {
            $pattern = str_replace('*', '.*', $except);
            if (preg_match('#^' . $pattern . '$#', $path)) {
                if (strpos($path, 'cancelar-cita') !== false || strpos($path, 'confirmar-cita') !== false) {
                    error_log('CSRFExceptWebhook: Ruta ' . $path . ' excluida del CSRF');
                    log_message('info', 'CSRFExceptWebhook: Ruta ' . $path . ' excluida del CSRF');
                }
                return; // No aplicar CSRF a esta ruta
            }
        }

        // Aplicar CSRF normal para otras rutas
        return parent::before($request, $arguments);
    }
}
