<?php

namespace App\Filters;

use CodeIgniter\Filters\CSRF as BaseCSRF;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro CSRF que excluye webhooks y rutas AJAX protegidas por sesión.
 */
class CSRFExceptWebhook extends BaseCSRF
{
    /**
     * Rutas excluidas del CSRF (path sin index.php, sin query string).
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
        'dashboard/agenda/guardarMediciones',
        'dashboard/agenda/guardarInformacionClinica',
        'dashboard/agenda/actualizarNotasNutricionista',
        'dashboard/agenda/aprobarReserva',
        'dashboard/historial/guardarInformacionClinica',
        'dashboard/historial/guardarMediciones',
        'dashboard/mi-perfil/subir-foto',
        'dashboard/mi-perfil/guardar',
        'dashboard/mi-perfil/perfil-publico',
        'dashboard/mi-perfil/credencial',
        'dashboard/mi-perfil/credencial/eliminar',
        'dashboard/historial/calcular-2-componentes',
        'dashboard/historial/calcular-4-componentes',
        'dashboard/historial/calcular-5-componentes',
        'dashboard/historial/calcular-somatotipo',
        'dashboard/gym/rutina/update-ejercicios',
        'dashboard/gym/programa/update-rutinas',
        'alumno/entrenamiento/*/serie',
    ];

    /** Patrones regex adicionales (path normalizado). */
    protected $exceptPatterns = [
        '#^dashboard/gym/(programa/update-rutinas|rutina/update-ejercicios)$#',
        '#^alumno/entrenamiento/\d+/serie$#',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $method = $request->getMethod();
        if (in_array(strtoupper($method), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return;
        }

        $paths = $this->collectPaths($request);
        foreach ($paths as $path) {
            if ($this->isExcluded($path)) {
                return;
            }
        }

        return parent::before($request, $arguments);
    }

    /**
     * @return list<string>
     */
    protected function collectPaths(RequestInterface $request): array
    {
        $paths = [];
        $uriPath = $this->normalizePath($request->getUri()->getPath());
        if ($uriPath !== '') {
            $paths[] = $uriPath;
        }

        try {
            $router = service('router');
            $matched = $router->getMatchedRoute();
            if (is_string($matched) && $matched !== '') {
                $paths[] = $this->normalizePath($matched);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return array_values(array_unique(array_filter($paths)));
    }

    protected function normalizePath(string $path): string
    {
        $path = preg_replace('#^https?://[^/]+#', '', $path) ?? $path;
        $path = preg_replace('#\?.*$#', '', $path) ?? $path;
        // Quitar index.php en cualquier posición del segmento inicial
        $path = preg_replace('#(?:^|/)index\.php(?=/|$)#', '', $path) ?? $path;
        $path = ltrim($path, '/');

        return $path;
    }

    protected function isExcluded(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        foreach ($this->except as $except) {
            $except = ltrim($except, '/');
            if ($path === $except) {
                return true;
            }

            $pattern = str_replace('*', '.*', $except);
            $pattern = str_replace('/', '\/', $pattern);
            if (preg_match('#^' . $pattern . '$#', $path)) {
                return true;
            }
        }

        foreach ($this->exceptPatterns as $regex) {
            if (preg_match($regex, $path)) {
                return true;
            }
        }

        if (preg_match('#(?:^|/)(actualizarNotasNutricionista|aprobarReserva)/?$#', $path)) {
            return true;
        }

        return false;
    }
}
