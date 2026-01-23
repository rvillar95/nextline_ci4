<?php

namespace App\Filters;

use CodeIgniter\Filters\CSRF as BaseCSRF;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro CSRF que excluye webhooks y rutas públicas
 */
class CSRFExceptWebhook extends BaseCSRF
{
    /**
     * Rutas excluidas del CSRF
     */
    protected $except = [
        'whatsapp/webhook',
        'whatsapp/webhook/*',
        'api/mercadopago/webhook',
        'api/mercadopago/webhook/*',
        'cancelar-cita',
        'confirmar-cita',
        'inicio-sesion'
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        // Normalizar el path: remover index.php/ si existe, remover query strings, remover dominio
        $path = preg_replace('#^https?://[^/]+#', '', $path); // Remover dominio
        $path = preg_replace('#^/?index\.php/#', '', $path); // Remover index.php/
        $path = preg_replace('#\?.*$#', '', $path); // Remover query strings
        $path = ltrim($path, '/');

        // Log para debugging de rutas excluidas (especialmente webhooks)
        if (strpos($path, 'mercadopago/webhook') !== false || 
            strpos($path, 'whatsapp/webhook') !== false ||
            strpos($path, 'cancelar-cita') !== false || 
            strpos($path, 'confirmar-cita') !== false) {
            error_log('CSRFExceptWebhook: Procesando ruta: ' . $path);
            error_log('CSRFExceptWebhook: Método: ' . $request->getMethod());
            error_log('CSRFExceptWebhook: URI completa: ' . (string)$uri);
            log_message('info', 'CSRFExceptWebhook: Procesando ruta: ' . $path);
            log_message('info', 'CSRFExceptWebhook: Método: ' . $request->getMethod());
            log_message('info', 'CSRFExceptWebhook: URI completa: ' . (string)$uri);
        }

        // Verificar si la ruta está excluida usando múltiples métodos
        foreach ($this->except as $except) {
            // Método 1: Match exacto
            if ($path === $except || $path === ltrim($except, '/')) {
                if (strpos($path, 'mercadopago/webhook') !== false || 
                    strpos($path, 'whatsapp/webhook') !== false) {
                    error_log('CSRFExceptWebhook: Ruta EXACTA ' . $path . ' excluida del CSRF');
                    log_message('info', 'CSRFExceptWebhook: Ruta EXACTA ' . $path . ' excluida del CSRF');
                }
                return; // No aplicar CSRF a esta ruta
            }
            
            // Método 2: Match con patrón (para wildcards)
            $pattern = str_replace('*', '.*', $except);
            $pattern = str_replace('/', '\/', $pattern); // Escapar slashes
            if (preg_match('#^/?' . $pattern . '$#', $path)) {
                if (strpos($path, 'mercadopago/webhook') !== false || 
                    strpos($path, 'whatsapp/webhook') !== false) {
                    error_log('CSRFExceptWebhook: Ruta PATRÓN ' . $path . ' excluida del CSRF (patrón: ' . $except . ')');
                    log_message('info', 'CSRFExceptWebhook: Ruta PATRÓN ' . $path . ' excluida del CSRF (patrón: ' . $except . ')');
                }
                return; // No aplicar CSRF a esta ruta
            }
            
            // Método 3: Match parcial (para rutas que contengan el patrón)
            if (strpos($path, $except) !== false || strpos($path, ltrim($except, '/')) !== false) {
                if (strpos($path, 'mercadopago/webhook') !== false || 
                    strpos($path, 'whatsapp/webhook') !== false) {
                    error_log('CSRFExceptWebhook: Ruta PARCIAL ' . $path . ' excluida del CSRF (contiene: ' . $except . ')');
                    log_message('info', 'CSRFExceptWebhook: Ruta PARCIAL ' . $path . ' excluida del CSRF (contiene: ' . $except . ')');
                }
                return; // No aplicar CSRF a esta ruta
            }
        }

        // Si es un webhook y no se excluyó, loguear para debugging
        if (strpos($path, 'mercadopago/webhook') !== false || strpos($path, 'whatsapp/webhook') !== false) {
            error_log('CSRFExceptWebhook: ADVERTENCIA - Webhook NO excluido: ' . $path);
            log_message('warning', 'CSRFExceptWebhook: ADVERTENCIA - Webhook NO excluido: ' . $path);
        }

        // Aplicar CSRF normal para otras rutas
        return parent::before($request, $arguments);
    }
}
