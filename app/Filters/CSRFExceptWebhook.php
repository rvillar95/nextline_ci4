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
        'inicio-sesion',
        'reservar/reservar',
        // Rutas AJAX de Plan Alimentario
        'dashboard/plan-alimentario/actividades',
        'dashboard/plan-alimentario/intercambios',
        'dashboard/plan-alimentario/calcular-calorimetria',
        'dashboard/plan-alimentario/crear-plan',
        'dashboard/plan-alimentario/distribuir-comidas',
        'dashboard/plan-alimentario/calorimetria/*',
        'dashboard/plan-alimentario/plan/*',
        'dashboard/plan-alimentario/comidas/*',
        'dashboard/plan-alimentario/get-paciente-data',
        'dashboard/plan-alimentario/vista-calorimetria',
        'dashboard/plan-alimentario/vista-plan',
        'dashboard/plan-alimentario/vista-distribucion',
        // Agenda: guardar mediciones, información clínica, notas y aprobar reserva (AJAX; protegido por sesión)
        'dashboard/agenda/guardarMediciones',
        'dashboard/agenda/guardarInformacionClinica',
        'dashboard/agenda/actualizarNotasNutricionista',
        'dashboard/agenda/aprobarReserva',
        // Historial editar: guardar por AJAX (igual que agenda/consulta; protegido por sesión)
        'dashboard/historial/guardarInformacionClinica',
        'dashboard/historial/guardarMediciones',
        // Mi perfil: subir foto y guardar preferencias (AJAX; protegido por sesión)
        'dashboard/mi-perfil/subir-foto',
        'dashboard/mi-perfil/guardar',
        'dashboard/mi-perfil/perfil-publico',
        'dashboard/mi-perfil/credencial',
        'dashboard/mi-perfil/credencial/eliminar',
        // Historial: cálculo de composición corporal (AJAX; protegido por sesión)
        'dashboard/historial/calcular-2-componentes',
        'dashboard/historial/calcular-4-componentes',
        'dashboard/historial/calcular-5-componentes',
        'dashboard/historial/calcular-somatotipo'
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // Las peticiones GET no requieren CSRF (son seguras por naturaleza)
        // Solo aplicar CSRF a métodos no seguros (POST, PUT, DELETE, PATCH)
        $method = $request->getMethod();
        if (in_array(strtoupper($method), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return; // No aplicar CSRF a métodos seguros
        }
        
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
                    strpos($path, 'whatsapp/webhook') !== false ||
                    strpos($path, 'plan-alimentario') !== false) {
                    error_log('CSRFExceptWebhook: Ruta PARCIAL ' . $path . ' excluida del CSRF (contiene: ' . $except . ')');
                    log_message('info', 'CSRFExceptWebhook: Ruta PARCIAL ' . $path . ' excluida del CSRF (contiene: ' . $except . ')');
                }
                return; // No aplicar CSRF a esta ruta
            }
        }

        // Método 4: Rutas que terminan en actualizarNotasNutricionista o aprobarReserva (por si el path lleva prefijo)
        if (preg_match('#(actualizarNotasNutricionista|aprobarReserva)/?$#', $path)) {
            return;
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
