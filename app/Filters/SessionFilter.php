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

        // 1) Públicos fuera del filtro
        foreach (self::PUBLIC_PATHS as $pub) {
            if ($this->matchesPattern($path, $pub)) {
                return;
            }
        }

        // 2) Requiere sesión
        $user = session('usuario');
        if ($user === null) {
            return redirect()->to(route_to('login'));
        }

        // 3) Permisos por perfil (con o sin caché)
        $perfilId = (int) ($user['perfil_id'] ?? 0);
        if ($perfilId <= 0) {
            return $this->deny('Perfil inválido o no asignado');
        }

        $allowed = $this->getAllowedRules($perfilId);

        // 4) Evaluar ruta contra patrones
        foreach ($allowed as $rule) {
            $pattern      = $rule['pattern'];              // p.ej. /dashboard/perfil/editar
            $allowTailNum = (bool)($rule['allowTailNum'] ?? false);
            $regex        = $rule['regex'];                // ya precompilado

            // Igualdad exacta (ignora slash final)
            if ($this->isDirectMatch($path, $pattern)) {
                return;
            }

            // Igualdad tras remover /<num> sólo si la regla lo permite
            if ($allowTailNum && $this->isDirectMatch($this->dropTrailingNum($path), $pattern)) {
                return;
            }

            // Match por placeholders CI4 a nivel de segmentos
            if ($this->matchesPattern($path, $pattern)) {
                return;
            }

            // Regex precompilado (incluye tolerancia a /<num> si corresponde)
            if ($this->pathMatchesRegex($path, $regex)) {
                return;
            }
        }

        // 5) Denegar si nada coincide
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

            $pattern  = $detRoute ? ($modRoute . $detRoute) : $modRoute;

            // Acciones efectivas
            $allowedActions = $this->effectiveActions($r['acciones_csv'] ?? null, $r['permisos'] ?? []);
            if (($r['acciones_csv'] ?? null) !== null && empty($allowedActions)) {
                // El detalle define acciones pero ninguna efectiva -> descartar
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
        $verbsId = ['editar', 'eliminar', 'update', 'detalle', 'show', 'view'];
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

    /** Denegación (mantengo tu patrón de redirect con flash) */
    private function deny(string $message)
    {
        return redirect()->back()->withInput()->with('errors', $message);
        // Alternativa API-friendly:
        // return service('response')->setStatusCode(403)->setBody($message);
    }
}
