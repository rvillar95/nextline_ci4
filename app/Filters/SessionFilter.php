<?php

declare(strict_types=1);

namespace App\Filters;

use App\Models\ModuloDetalle;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class SessionFilter implements FilterInterface
{
    /** Rutas públicas que no requieren sesión/permiso. Ajusta según tu proyecto. */
    private const PUBLIC_PATHS = [
        '/',
        '/login',
        '/login/(:any)',
        '/logout',
        '/password/(:any)',
        '/css/(:any)',
        '/js/(:any)',
        '/images/(:any)',
        '/img/(:any)',
        '/assets/(:any)',
        '/favicon.ico',
        // Rutas AJAX de ubicación (regiones/comunas)
        '/dashboard/ubicacion/regiones',
        '/dashboard/ubicacion/comunas/(:any)',
        '/dashboard/ubicacion/buscar-comunas',
        '/dashboard/ubicacion/comuna-info/(:any)',
        '/dashboard/ubicacion/validar',
        '/dashboard/ubicacion/estadisticas',
        // Reserva pública (paciente reserva hora sin login)
        '/reservar',
        '/reservar/disponibilidad',
        '/reservar/paciente-por-rut',
        '/reservar/reservar',
        // Rutas públicas para confirmar/cancelar citas desde email
        '/confirmar-cita',
        '/cancelar-cita',
        // Callback público de OAuth2 para calendario (Google/Microsoft llama esta URL directamente)
        '/dashboard/agenda/calendario/callback',
        // Webhook WhatsApp (Meta, sin sesión)
        '/whatsapp/webhook',
    ];

    /**
     * TTL del caché de permisos (segundos).
     *  - 0  => SIN caché (recomiendo en desarrollo)
     *  - >0 => con caché (recomiendo en producción, p.ej. 300)
     * Usa PermissionCache::invalidatePerfil / ::invalidateAll para efecto inmediato.
     */
    private const PERM_CACHE_TTL = 0;

    public function before(RequestInterface $request, $arguments = null)
    {
        $path = $this->sanitizePath($this->currentPath($request));
        
        // Log para debugging de cancelar-cita y confirmar-cita
        if (strpos($path, 'cancelar-cita') !== false || strpos($path, 'confirmar-cita') !== false) {
            error_log('========================================');
            error_log('SessionFilter: Procesando ruta: ' . $path);
            error_log('SessionFilter: URI completa: ' . (string)$request->getUri());
            error_log('SessionFilter: Método: ' . $request->getMethod());
            log_message('info', '========================================');
            log_message('info', 'SessionFilter: Procesando ruta: ' . $path);
            log_message('info', 'SessionFilter: URI completa: ' . (string)$request->getUri());
            log_message('info', 'SessionFilter: Método: ' . $request->getMethod());
        }
        
        // Log para debugging de calendario
        /*
        if (strpos($path, 'calendar') !== false) {
            log_message('info', '========================================');
            log_message('info', 'SessionFilter: Procesando ruta de calendario: ' . $path);
            log_message('info', '========================================');
        }*/
        
        // Log para debugging
        if (strpos($path, 'generarPDF') !== false) {
            log_message('debug', 'SessionFilter: Procesando ruta generarPDF: ' . $path);
        }
        
        // Log para debugging de paquetes
        if (strpos($path, 'paquete') !== false) {
            log_message('info', 'SessionFilter: Procesando ruta de paquetes: ' . $path);
        }

        // 1) Públicos fuera del filtro
        foreach (self::PUBLIC_PATHS as $pub) {
            if ($this->matchesPattern($path, $pub)) {
                if (strpos($path, 'cancelar-cita') !== false || strpos($path, 'confirmar-cita') !== false) {
                    error_log('SessionFilter: Ruta ' . $path . ' encontrada en PUBLIC_PATHS - PERMITIDA');
                    log_message('info', 'SessionFilter: Ruta ' . $path . ' encontrada en PUBLIC_PATHS - PERMITIDA');
                }
                if (strpos($path, 'generarPDF') !== false) {
                    log_message('debug', 'SessionFilter: Ruta generarPDF encontrada en PUBLIC_PATHS');
                }
                return;
            }
        }
        
        // Log si no se encontró en PUBLIC_PATHS
        if (strpos($path, 'cancelar-cita') !== false || strpos($path, 'confirmar-cita') !== false) {
            error_log('SessionFilter: Ruta ' . $path . ' NO encontrada en PUBLIC_PATHS - Continuando con validación');
            log_message('warning', 'SessionFilter: Ruta ' . $path . ' NO encontrada en PUBLIC_PATHS - Continuando con validación');
        }

        // 2) Requiere sesión
        $user = session('usuario');
        if ($user === null) {
            // Si es AJAX, devolver JSON en lugar de redirect
            if ($request->isAJAX() || $request->hasHeader('X-Requested-With')) {
                return service('response')
                    ->setContentType('application/json')
                    ->setStatusCode(401)
                    ->setJSON([
                        'error' => 'No autorizado',
                        'unauthorized' => true,
                        'redirect' => route_to('login')
                    ]);
            }
            return redirect()->to(route_to('login'));
        }

        // 2.5) Suscripción vencida (no aplica a Super Admin ni plan partner)
        if ((int) ($user['poder'] ?? 0) !== 3) {
            $suscripcionBlock = $this->checkSuscripcionVencida($user, $path, $request);
            if ($suscripcionBlock !== null) {
                return $suscripcionBlock;
            }
        }

        // 3) Permisos por perfil (con o sin caché)
        $perfilId = (int) ($user['perfil_id'] ?? 0);
        if ($perfilId <= 0) {
            // Si es AJAX, devolver JSON en lugar de redirect
            if ($request->isAJAX() || $request->hasHeader('X-Requested-With')) {
                return service('response')
                    ->setContentType('application/json')
                    ->setStatusCode(403)
                    ->setJSON([
                        'error' => 'Perfil inválido o no asignado',
                        'unauthorized' => true
                    ]);
            }
            return $this->deny('Perfil inválido o no asignado');
        }

        $allowed = $this->getAllowedRules($perfilId);
        
        if (strpos($path, 'generarPDF') !== false) {
            log_message('debug', 'SessionFilter: Reglas permitidas para perfil ' . $perfilId . ': ' . count($allowed));
            foreach ($allowed as $rule) {
                log_message('debug', 'SessionFilter: Regla: ' . $rule['pattern'] . ' (allowTailNum: ' . ($rule['allowTailNum'] ? 'true' : 'false') . ')');
            }
        }

        // 4) Evaluar ruta contra patrones
        // Log temporal para debugging de agenda
        if (strpos($path, 'agenda/agendar') !== false) {
            //log_message('debug', 'SessionFilter: Evaluando ruta: ' . $path);
            //log_message('debug', 'SessionFilter: Total de reglas permitidas: ' . count($allowed));
            foreach ($allowed as $idx => $rule) {
                if (strpos($rule['pattern'], 'agenda') !== false) {
                    log_message('debug', "SessionFilter: Regla #{$idx}: pattern={$rule['pattern']}, allowTailNum=" . ($rule['allowTailNum'] ? 'true' : 'false'));
                }
            }
        }
        
        foreach ($allowed as $rule) {
            $pattern      = $rule['pattern'];              // p.ej. /dashboard/perfil/editar
            $allowTailNum = (bool)($rule['allowTailNum'] ?? false);
            $regex        = $rule['regex'];                // ya precompilado
            //            //echo $pattern." vs ".$path."<br>";
            // Igualdad exacta (ignora slash final)
            if ($this->isDirectMatch($path, $pattern)) {
                if (strpos($path, 'generarPDF') !== false || strpos($path, 'agenda/agendar') !== false) {
                    log_message('debug', 'SessionFilter: Ruta ' . $path . ' PERMITIDA por igualdad exacta con: ' . $pattern);
                }
                return;
            }

            // Igualdad tras remover /<num> sólo si la regla lo permite
            if ($allowTailNum && $this->isDirectMatch($this->dropTrailingNum($path), $pattern)) {
                if (strpos($path, 'generarPDF') !== false) {
                    log_message('debug', 'SessionFilter: Ruta generarPDF PERMITIDA por igualdad con tail num con: ' . $pattern);
                }
                return;
            }

            // Match por placeholders CI4 a nivel de segmentos
            if ($this->matchesPattern($path, $pattern)) {
                if (strpos($path, 'generarPDF') !== false) {
                    log_message('debug', 'SessionFilter: Ruta generarPDF PERMITIDA por match pattern con: ' . $pattern);
                }
                return;
            }

            // Regex precompilado (incluye tolerancia a /<num> si corresponde)
            if ($this->pathMatchesRegex($path, $regex)) {
                if (strpos($path, 'generarPDF') !== false) {
                    log_message('debug', 'SessionFilter: Ruta generarPDF PERMITIDA por regex con: ' . $pattern);
                }
                return;
            }
        }
        //exit();

        // 5) Excepciones especiales para rutas que requieren autenticación pero no están en módulo_detalle
        // PENDIENTE OBLIGATORIO (docs/PENDIENTES.md P1): registrar en modulo_detalle y quitar bypasses
        // que no respeten ver/registrar/editar/eliminar del perfil.
        // Rutas de calendario/agenda que requieren autenticación (usuario logueado con acceso a agenda)
        $calendarioExcepciones = [
            '/dashboard/agenda/calendario/verificar-token',
            '/dashboard/agenda/calendario/connect',
            '/dashboard/agenda/actualizarNotasNutricionista',
            '/dashboard/agenda/guardarInformacionClinica',
            '/dashboard/agenda/guardarMediciones',
            '/dashboard/agenda/consulta',
            '/dashboard/notificaciones/listar',
            '/dashboard/notificaciones/marcar-leida',
            '/dashboard/notificaciones/marcar-todas-leidas',
        ];
        
        foreach ($calendarioExcepciones as $excepcion) {
            if ($this->isDirectMatch($path, $excepcion)) {
                // Verificar que el usuario tenga acceso al módulo de agenda
                // Si tiene acceso a agenda, permitir estas rutas de calendario
                $tieneAccesoAgenda = false;
                foreach ($allowed as $rule) {
                    if (strpos($rule['pattern'], '/dashboard/agenda') === 0) {
                        $tieneAccesoAgenda = true;
                        break;
                    }
                }
                
                if ($tieneAccesoAgenda) {
                    log_message('info', 'SessionFilter: Ruta de calendario permitida por excepción: ' . $path);
                    return;
                }
            }
        }

        // Mensajes WhatsApp: hilo/{id} y enviar si tiene acceso al módulo mensajes
        $esMensajesAjax = (bool) preg_match('#^/dashboard/mensajes/hilo/[0-9]+/?$#', $this->sanitizePath($path))
            || $this->isDirectMatch($path, '/dashboard/mensajes/enviar')
            || $this->isDirectMatch($path, '/dashboard/mensajes/sync');
        if ($esMensajesAjax) {
            foreach ($allowed as $rule) {
                if (strpos($rule['pattern'], '/dashboard/mensajes') === 0) {
                    log_message('info', 'SessionFilter: Ruta mensajes permitida por excepción: ' . $path);
                    return;
                }
            }
        }

        // Plan alimentario: sub-rutas AJAX si tiene acceso al módulo
        $pathPlan = $this->sanitizePath($path);
        if ($pathPlan === '/dashboard/plan-alimentario'
            || strpos($pathPlan, '/dashboard/plan-alimentario/') === 0) {
            foreach ($allowed as $rule) {
                if (strpos($rule['pattern'], '/dashboard/plan-alimentario') === 0
                    || str_contains($rule['pattern'], 'plan-alimentario')
                    || str_contains($rule['pattern'], 'plan_alimentario')) {
                    return;
                }
            }
        }

        // Documentos: AJAX (envío por correo, listado por paciente, eliminar) si tiene el módulo
        $esDocumentoAjax = $this->isDirectMatch($path, '/dashboard/documento/enviar-correo')
            || $this->isDirectMatch($path, '/dashboard/documento/registrar-lote')
            || $this->isDirectMatch($path, '/dashboard/documento/getDocumentos')
            || (bool) preg_match('#^/dashboard/documento/por-paciente/[0-9]+/?$#', $this->sanitizePath($path))
            || (bool) preg_match('#^/dashboard/documento/eliminar/[0-9]+/?$#', $this->sanitizePath($path))
            || (bool) preg_match('#^/dashboard/documento/enviar/[0-9]+/?$#', $this->sanitizePath($path))
            || (bool) preg_match('#^/dashboard/documento/[0-9]+/descargar/?$#', $this->sanitizePath($path))
            || (bool) preg_match('#^/dashboard/documento/[0-9]+/enlace-compartir/?$#', $this->sanitizePath($path));
        if ($esDocumentoAjax) {
            foreach ($allowed as $rule) {
                if (strpos($rule['pattern'], '/dashboard/documento') === 0) {
                    return;
                }
            }
        }

        // Tarifas: /editar/{id} (ruta legacy; edición habitual vía /crear?plantilla_id=)
        if (preg_match('#^/dashboard/boton-pago/editar/[0-9]+/?$#', $this->sanitizePath($path))) {
            foreach ($allowed as $rule) {
                if (strpos($rule['pattern'], '/dashboard/boton-pago') === 0) {
                    return;
                }
            }
        }

        // Cobros a pacientes: /pago/cobros si tiene acceso al módulo pago
        if ($this->isDirectMatch($path, '/dashboard/pago/cobros')) {
            foreach ($allowed as $rule) {
                if (strpos($rule['pattern'], '/dashboard/pago') === 0) {
                    return;
                }
            }
        }

        // Rutas AJAX/acciones del módulo Empresa (getEmpresas, eliminar, activar) permitidas si tiene acceso a empresa
        $empresaExcepcionPrefijo = '/dashboard/empresa/eliminar/';
        $empresaExcepcionPrefijo2 = '/dashboard/empresa/activar/';
        $esEmpresaExcepcion = $this->isDirectMatch($path, '/dashboard/empresa/getEmpresas')
            || (strpos($path, $empresaExcepcionPrefijo) === 0 && preg_match('#^/dashboard/empresa/eliminar/[0-9]+/?$#', $this->sanitizePath($path)))
            || (strpos($path, $empresaExcepcionPrefijo2) === 0 && preg_match('#^/dashboard/empresa/activar/[0-9]+/?$#', $this->sanitizePath($path)));
        if ($esEmpresaExcepcion) {
            foreach ($allowed as $rule) {
                if (strpos($rule['pattern'], '/dashboard/empresa') === 0) {
                    log_message('info', 'SessionFilter: Ruta empresa permitida por excepción: ' . $path);
                    return;
                }
            }
        }

        // Menú lateral (super admin): rutas menu-grupo si tiene acceso al módulo Módulos
        if (strpos($this->sanitizePath($path), '/dashboard/menu-grupo') === 0) {
            foreach ($allowed as $rule) {
                if (strpos($rule['pattern'], '/dashboard/modulo') === 0) {
                    return;
                }
            }
        }

        // 6) Denegar si nada coincide
        // Log para debugging de plan-alimentario
        if (strpos($path, 'plan-alimentario') !== false) {
            log_message('error', 'SessionFilter: Ruta ' . $path . ' DENEGADA - no tiene permisos');
            log_message('error', 'SessionFilter: Total de reglas permitidas: ' . count($allowed));
            log_message('error', 'SessionFilter: Rutas permitidas que contienen "plan-alimentario":');
            foreach ($allowed as $rule) {
                if (strpos($rule['pattern'], 'plan-alimentario') !== false) {
                    log_message('error', '  - ' . $rule['pattern']);
                }
            }
        }
        
        if (strpos($path, 'generarPDF') !== false || strpos($path, 'agenda/agendar') !== false || strpos($path, 'calendar') !== false || strpos($path, 'calendario') !== false || strpos($path, 'paquete') !== false) {
            log_message('error', 'SessionFilter: Ruta ' . $path . ' DENEGADA - no tiene permisos');
            log_message('error', 'SessionFilter: Total de reglas permitidas: ' . count($allowed));
            if (strpos($path, 'paquete') !== false) {
                log_message('error', 'SessionFilter: Rutas permitidas que contienen "paquete":');
                foreach ($allowed as $rule) {
                    if (strpos($rule['pattern'], 'paquete') !== false) {
                        log_message('error', '  - ' . $rule['pattern']);
                    }
                }
            }
            //log_message('error', 'SessionFilter: Rutas permitidas que contienen "agenda" o "calendar":');
            foreach ($allowed as $rule) {
                if (strpos($rule['pattern'], 'agenda') !== false || strpos($rule['pattern'], 'calendar') !== false || strpos($rule['pattern'], 'calendario') !== false) {
                    log_message('error', '  - ' . $rule['pattern']);
                }
            }
        }
        return $this->deny('No tiene permisos para esta funcionalidad');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No-op
    }

    /**
     * Obtiene las reglas permitidas para un perfil, usando cache versionado
     * cuando PERM_CACHE_TTL > 0.
     * @return array<int, array{pattern:string, allowTailNum:bool, regex:string}>
     */
    private function getAllowedRules(int $perfilId): array
    {
        // Versión global del mapa (incrementada por PermissionCache::invalidateAll)
        $version = (int) (cache('perm_version') ?? 1);
        $cacheKey = "allowed_routes_v{$version}_{$perfilId}";

        if (self::PERM_CACHE_TTL > 0) {
            return cache()->remember($cacheKey, self::PERM_CACHE_TTL, function () use ($perfilId): array {
                $md = new ModuloDetalle();
                return $this->loadAllowedMap($md->getAllowedByPerfil($perfilId));
            });
        }

        // Sin caché (dev)
        $md = new ModuloDetalle();
        return $this->loadAllowedMap($md->getAllowedByPerfil($perfilId));
    }

    /**
     * Transforma filas de BD en reglas:
     *  - pattern normalizado
     *  - allowTailNum (si la acción sugiere ID al final, p.ej. editar/eliminar)
     *  - regex base (sin o con /<num> opcional según allowTailNum)
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array{pattern:string, allowTailNum:bool, regex:string}>
     */
    private function loadAllowedMap(array $rows): array
    {
        $rules = [];

        foreach ($rows as $r) {
            $modRoute = $this->normalizeRoute((string) ($r['modulo_ruta'] ?? ''));
            $detRoute = isset($r['detalle_ruta']) && $r['detalle_ruta'] !== null
                ? $this->normalizeRoute((string) $r['detalle_ruta'])
                : null;

            // Si modulo_ruta viene vacío, ignora fila
            if ($modRoute === '') {
                continue;
            }

            // Si detRoute es una ruta absoluta (empieza con /dashboard/), usarla directamente
            // Si no, concatenarla con modRoute (ruta relativa)
            if ($detRoute && (strpos($detRoute, '/dashboard/') === 0 || strpos($detRoute, 'dashboard/') === 0)) {
                $pattern = $detRoute; // Ruta absoluta - usar directamente
            } else {
                $pattern = $detRoute ? ($modRoute . $detRoute) : $modRoute; // Ruta relativa - concatenar
            }

            // Acciones efectivas
            $allowedActions = $this->effectiveActions($r['acciones_csv'] ?? null, $r['permisos'] ?? []);
            
            // Log temporal para debugging de agenda
            if (isset($r['detalle_ruta']) && strpos($r['detalle_ruta'], 'agendar') !== false) {
                log_message('debug', 'SessionFilter: Procesando detalle agendar');
                log_message('debug', '  - modulo_ruta: ' . ($r['modulo_ruta'] ?? 'N/A'));
                log_message('debug', '  - detalle_ruta: ' . ($r['detalle_ruta'] ?? 'N/A'));
                log_message('debug', '  - acciones_csv: ' . ($r['acciones_csv'] ?? 'N/A'));
                log_message('debug', '  - permisos: ' . json_encode($r['permisos'] ?? []));
                log_message('debug', '  - allowedActions: ' . json_encode($allowedActions));
            }
            
            if (($r['acciones_csv'] ?? null) !== null && empty($allowedActions)) {
                // El detalle define acciones pero ninguna efectiva -> descartar
                if (isset($r['detalle_ruta']) && strpos($r['detalle_ruta'], 'agendar') !== false) {
                    log_message('debug', 'SessionFilter: DESCARTA regla agendar porque allowedActions está vacío');
                }
                continue;
            }

            $clean = $this->sanitizePath($pattern);
            $allowTailNum = $this->shouldAllowTailNum($allowedActions, $clean);

            $rules[] = [
                'pattern'      => $clean,
                'allowTailNum' => $allowTailNum,
                'regex'        => $this->buildRegexFromPattern($clean, $allowTailNum),
            ];
        }

        // Deduplicar por 'pattern'
        $uniq = [];
        $out  = [];
        foreach ($rules as $row) {
            if (!isset($uniq[$row['pattern']])) {
                $uniq[$row['pattern']] = true;
                $out[] = $row;
            }
        }
        return $out;
    }

    /** Acciones efectivas = intersección acciones CSV del detalle con flags del perfil en el módulo. */
    private function effectiveActions(?string $accionesCsv, array $permisos): array
    {
        if ($accionesCsv === null || trim($accionesCsv) === '') {
            return ($permisos['ver'] ?? false) ? ['ver'] : [];
        }

        $acciones = array_filter(array_map('trim', explode(',', $accionesCsv)));
        $efectivas = [];

        foreach ($acciones as $a) {
            $aKey = match ($a) {
                'ver'       => 'ver',
                'registrar' => 'registrar',
                'editar'    => 'editar',
                'eliminar'  => 'eliminar',
                default     => null,
            };
            if ($aKey !== null && ($permisos[$aKey] ?? false)) {
                $efectivas[] = $a;
            }
        }
        return $efectivas;
    }

    /** Heurística: ¿esta regla debería tolerar un /<num> al final? */
    private function shouldAllowTailNum(array $allowedActions, string $pattern): bool
    {
        // 1) Si la acción efectiva incluye editar/eliminar -> suele llevar ID
        if (array_intersect($allowedActions, ['editar', 'eliminar'])) {
            return true;
        }
        // 2) Si el patrón ya termina con placeholder dinámico, no hace falta (ya lo acepta)
        if ($this->hasDynamicTail($pattern)) {
            return false;
        }
        // 3) Heurística por último segmento (evita 'lista'/'registro')
        $last = basename($pattern);
        $verbsId = ['editar', 'eliminar', 'activar', 'update', 'detalle', 'show', 'view', 'generarPDF', 'calorimetria', 'plan', 'comidas'];
        return in_array($last, $verbsId, true);
    }

    /** ¿El patrón termina con (:num) o (:segment) o (:any)? */
    private function hasDynamicTail(string $pattern): bool
    {
        return (bool) preg_match('#/(\(:num\)|\(:segment\)|\(:any\))/?$#', $pattern);
    }

    /** Normaliza rutas a formato '/segment/segment' */
    private function normalizeRoute(string $r): string
    {
        $r = '/' . ltrim(trim($r), '/');
        return $r !== '/' ? rtrim($r, '/') : $r;
    }

    /** Limpia caracteres invisibles y normaliza slashes en rutas */
    private function sanitizePath(string $path): string
    {
        $path = str_replace("\xC2\xA0", ' ', $path);       // NBSP
        $path = str_replace(["\r", "\n", "\t"], '', $path);
        return $this->normalizeRoute(trim($path));
    }

    /** Elimina un único segmento numérico al final, si existe */
    private function dropTrailingNum(string $path): string
    {
        return preg_replace('#/[0-9]+/?$#', '', $this->sanitizePath($path)) ?: '/';
    }

    /** Igualdad directa (ignora slash final) */
    private function isDirectMatch(string $path, string $pattern): bool
    {
        return rtrim($path, '/') === rtrim($pattern, '/');
    }

    /**
     * Match por segmentos interpretando placeholders CI4:
     *  - (:num)     -> ctype_digit
     *  - (:segment) -> string sin '/'
     *  - (:any)     -> consume el resto (si está al final)
     */
    private function matchesPattern(string $path, string $pattern): bool
    {
        $path    = $this->sanitizePath($path);
        $pattern = $this->sanitizePath($pattern);

        if ($this->isDirectMatch($path, $pattern)) {
            return true;
        }

        $pSegs = $path === '/' ? [] : explode('/', ltrim($path, '/'));
        $tSegs = $pattern === '/' ? [] : explode('/', ltrim($pattern, '/'));

        $pCount = count($pSegs);
        $tCount = count($tSegs);

        $i = 0;
        while ($i < $tCount && $i < $pCount) {
            $t = $tSegs[$i];
            $s = $pSegs[$i];

            if ($t === '(:num)') {
                if ($s === '' || !ctype_digit($s)) return false;
            } elseif ($t === '(:segment)') {
                if ($s === '' || strpos($s, '/') !== false) return false;
            } elseif ($t === '(:any)') {
                return true; // todo lo que reste
            } else {
                if ($t !== $s) return false;
            }
            $i++;
        }

        if ($i < $tCount) {
            // Si el siguiente es (:any) y es el último, OK
            if ($tSegs[$i] === '(:any)' && $i === $tCount - 1) {
                return true;
            }
            return false;
        }

        if ($i < $pCount) {
            // path tiene resto; sólo válido si patrón termina con (:any)
            if ($tCount > 0 && $tSegs[$tCount - 1] === '(:any)') {
                return true;
            }
            return false;
        }

        return true;
    }

    /**
     * Construye regex con soporte de placeholders y, opcionalmente,
     * agrega '/<num>' final como opcional si $allowTailNum = true.
     */
    private function buildRegexFromPattern(string $pattern, bool $allowTailNum = false): string
    {
        $p = rtrim($this->sanitizePath($pattern), '/');
        $p = preg_quote($p, '#');
        $map = [
            '\(\:num\)'     => '[0-9]+',
            '\(\:segment\)' => '[^/]+',
            '\(\:any\)'     => '.+',
        ];
        $p = strtr($p, $map);

        if ($allowTailNum && !$this->hasDynamicTail($pattern)) {
            // Acepta un único segmento numérico al final
            $p .= '(?:/[0-9]+)?';
        }

        return '#^' . $p . '/?$#u';
    }

    private function pathMatchesRegex(string $path, string $regex): bool
    {
        $p = '/' . trim($path, '/');
        if ($p === '//') $p = '/';
        return preg_match($regex, $p) === 1;
    }

    /**
     * Devuelve la ruta de aplicación, quitando subcarpeta base y/o index.php.
     * Ej: /app/sub/index.php/dashboard/menu -> /dashboard/menu
     */
    private function currentPath(RequestInterface $request): string
    {
        $uri = $request->getUri();
        $raw = '/' . ltrim($uri->getPath(), '/');

        // quitar basePath si baseURL tiene subcarpeta
        $baseURL  = rtrim((string) config('App')->baseURL, '/');
        $basePath = (string) (parse_url($baseURL, PHP_URL_PATH) ?? '');
        if ($basePath !== '' && str_starts_with($raw, $basePath)) {
            $raw = substr($raw, strlen($basePath));
            if ($raw === '' || $raw[0] !== '/') $raw = '/' . $raw;
        }

        // quitar index.php si aparece
        $index = trim((string) config('App')->indexPage, '/'); // 'index.php' o ''
        if ($index !== '' && str_starts_with($raw, '/' . $index)) {
            $raw = substr($raw, strlen('/' . $index));
            if ($raw === '' || $raw[0] !== '/') $raw = '/' . $raw;
        }

        $raw = '/' . trim($raw, '/');
        return $raw === '//' ? '/' : $raw;
    }

    /**
     * Bloquea acceso si la empresa tiene suscripción registrada y no está activa.
     * Sin fila en suscripciones = comportamiento legacy (solo paquete).
     */
    private function checkSuscripcionVencida(array $user, string $path, RequestInterface $request): ?ResponseInterface
    {
        $exemptPatterns = [
            '/dashboard/menu',
            '/logout',
            '/dashboard/mi-perfil',
        ];
        foreach ($exemptPatterns as $pattern) {
            if ($this->matchesPattern($path, $pattern)) {
                return null;
            }
        }

        $empresaId = (int) ($user['empresa_id'] ?? 0);
        if ($empresaId <= 0) {
            return null;
        }

        $db = \Config\Database::connect();
        $paquete = $db->table('empresa e')
            ->select('p.slug, p.precio_mensual')
            ->join('paquetes p', 'p.id = e.paquete_id', 'left')
            ->where('e.id', $empresaId)
            ->get()
            ->getRow();

        if ($paquete && ($paquete->slug === 'nutri-partner' || (float) $paquete->precio_mensual <= 0)) {
            return null;
        }

        $suscripcionModel = new \App\Models\Suscripcion();
        $ultima           = $suscripcionModel->where('empresa_id', $empresaId)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$ultima) {
            return null;
        }

        if ($suscripcionModel->estaActiva($empresaId)) {
            return null;
        }

        $message = 'Tu suscripción no está activa. Contacta a soporte para renovar el plan.';

        if ($request->isAJAX() || $request->hasHeader('X-Requested-With')) {
            return service('response')
                ->setContentType('application/json')
                ->setStatusCode(403)
                ->setJSON([
                    'error' => $message,
                    'suscripcion_vencida' => true,
                ]);
        }

        return redirect()->to(base_url('dashboard/menu'))
            ->with('error', $message);
    }

    /** Denegación (mantengo tu patrón de redirect con flash) */
    private function deny(string $message)
    {
        // Si es una petición AJAX, devolver JSON en lugar de redirect
        $request = service('request');
        if ($request->isAJAX() || $request->hasHeader('X-Requested-With')) {
            return service('response')
                ->setContentType('application/json')
                ->setStatusCode(403)
                ->setJSON([
                    'error' => $message,
                    'unauthorized' => true
                ]);
        }
        
        return redirect()->back()->withInput()->with('errors', $message);
    }
}
