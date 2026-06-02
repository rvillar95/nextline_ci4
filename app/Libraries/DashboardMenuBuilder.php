<?php

namespace App\Libraries;

use App\Models\MenuGrupo;

/**
 * Construye el menú lateral desde configuración en BD (menu_grupo + campos en modulo / modulo_detalle).
 */
class DashboardMenuBuilder
{
    /** @var array<int, array{slug: string, etiqueta: string, orden: int}>|null */
    private static ?array $gruposCache = null;

    /**
     * @param array<int, array{menu: array, submenu: array}> $menuItems
     * @return list<array{type: string, label?: string, href?: string, icon?: string, children?: list<array{label: string, href: string}>}>
     */
    public static function build(array $menuItems, ?array $usuario = null): array
    {
        $usuario = $usuario ?? (session()->get('usuario') ?? []);
        $esSuperAdmin = ((int) ($usuario['poder'] ?? 0)) === 3;

        $grupos = self::loadGrupos();
        $bucket = [];
        foreach ($grupos as $g) {
            $bucket[$g['slug']] = [];
        }
        if (! isset($bucket['otros'])) {
            $bucket['otros'] = [];
        }

        foreach ($menuItems as $menu) {
            $mod = $menu['menu'] ?? [];

            if (! $esSuperAdmin && (($mod['menu_solo_sa'] ?? 'N') === 'S')) {
                continue;
            }

            $entry = self::buildModuleEntry($menu, $esSuperAdmin);
            if ($entry === null) {
                continue;
            }

            $slug = trim((string) ($mod['menu_grupo_slug'] ?? ''));
            if ($slug === '' || ! isset($bucket[$slug])) {
                $slug = 'otros';
            }
            $bucket[$slug][] = $entry;
        }

        $flat = [];
        $slugsEnGrupos = array_column($grupos, 'slug');

        foreach ($grupos as $g) {
            $slug = $g['slug'];
            if (empty($bucket[$slug])) {
                continue;
            }
            $flat[] = ['type' => 'heading', 'label' => $g['etiqueta']];
            foreach ($bucket[$slug] as $entry) {
                $flat[] = $entry;
            }
        }

        // Solo si "otros" no está en menu_grupo (fallback sin tabla configurada)
        if (! empty($bucket['otros']) && ! in_array('otros', $slugsEnGrupos, true)) {
            $flat[] = ['type' => 'heading', 'label' => 'Más'];
            foreach ($bucket['otros'] as $entry) {
                $flat[] = $entry;
            }
        }

        return $flat;
    }

    /**
     * @return list<array{slug: string, etiqueta: string, orden: int}>
     */
    private static function loadGrupos(): array
    {
        if (self::$gruposCache !== null) {
            return self::$gruposCache;
        }

        try {
            $model = new MenuGrupo();
            if (! $model->db->tableExists('menu_grupo')) {
                return self::$gruposCache = self::gruposFallback();
            }
            $rows = $model->getActivosOrdenados();
            if (empty($rows)) {
                return self::$gruposCache = self::gruposFallback();
            }
            self::$gruposCache = array_map(static fn ($r) => [
                'slug' => (string) $r['slug'],
                'etiqueta' => (string) $r['etiqueta'],
                'orden' => (int) $r['orden'],
            ], $rows);

            return self::$gruposCache;
        } catch (\Throwable $e) {
            log_message('error', 'DashboardMenuBuilder::loadGrupos: ' . $e->getMessage());

            return self::$gruposCache = self::gruposFallback();
        }
    }

    /** @return list<array{slug: string, etiqueta: string, orden: int}> */
    private static function gruposFallback(): array
    {
        return [
            ['slug' => 'operacion', 'etiqueta' => 'Día a día', 'orden' => 10],
            ['slug' => 'pacientes', 'etiqueta' => 'Pacientes', 'orden' => 20],
            ['slug' => 'finanzas', 'etiqueta' => 'Cobros', 'orden' => 30],
            ['slug' => 'equipo', 'etiqueta' => 'Mi equipo', 'orden' => 35],
            ['slug' => 'cuenta', 'etiqueta' => 'Cuenta', 'orden' => 40],
            ['slug' => 'admin', 'etiqueta' => 'Administración', 'orden' => 50],
        ];
    }

    /**
     * @return array{type: string, label: string, href?: string, icon: string, children?: list<array{label: string, href: string}>}|null
     */
    private static function buildModuleEntry(array $menu, bool $esSuperAdmin): ?array
    {
        $mod = $menu['menu'] ?? [];
        $modNombre = (string) ($mod['nombre'] ?? 'Módulo');
        $icon = self::sanitizeIcon((string) ($mod['menu_icono'] ?? 'circle'));

        if (! $esSuperAdmin && ! empty($mod['menu_ruta_alterna'])) {
            $label = trim((string) ($mod['menu_etiqueta_alterna'] ?? ''));
            if ($label === '') {
                $label = self::moduleLabel($mod);
            }

            return [
                'type'  => 'link',
                'label' => $label,
                'href'  => self::normalizeModuleHref((string) $mod['menu_ruta_alterna']),
                'icon'  => $icon,
            ];
        }

        $visibleSubs = self::collectVisibleSubmenus($menu);
        $aplanar = ($mod['menu_aplanar'] ?? 'S') === 'S';

        if ($aplanar && count($visibleSubs) === 1) {
            $sub = $visibleSubs[0];

            return [
                'type'  => 'link',
                'label' => $sub['label'] ?: self::moduleLabel($mod),
                'href'  => $sub['href'],
                'icon'  => $icon,
            ];
        }

        if (count($visibleSubs) > 1) {
            $children = [];
            foreach ($visibleSubs as $sub) {
                $children[] = ['label' => $sub['label'], 'href' => $sub['href']];
            }

            return [
                'type'     => 'group',
                'label'    => self::moduleLabel($mod),
                'icon'     => $icon,
                'children' => $children,
            ];
        }

        if (! empty($mod['ver']) && ! empty($mod['ruta'])) {
            return [
                'type'  => 'link',
                'label' => self::moduleLabel($mod),
                'href'  => self::normalizeModuleHref((string) $mod['ruta']),
                'icon'  => $icon,
            ];
        }

        return null;
    }

    private static function moduleLabel(array $mod): string
    {
        $etiqueta = trim((string) ($mod['menu_etiqueta'] ?? ''));

        return $etiqueta !== '' ? $etiqueta : (string) ($mod['nombre'] ?? 'Módulo');
    }

    /**
     * @return list<array{label: string, href: string}>
     */
    private static function collectVisibleSubmenus(array $menu): array
    {
        $mod = $menu['menu'] ?? [];
        $out = [];

        foreach ($menu['submenu'] ?? [] as $submenu) {
            $acciones = array_filter(array_map('trim', explode(',', (string) ($submenu['accion'] ?? ''))));
            $mostrar = false;
            foreach ($acciones as $accion) {
                if ($accion !== '' && ! empty($mod[$accion])) {
                    $mostrar = true;
                    break;
                }
            }
            if (! $mostrar || ($submenu['mostrar'] ?? 'N') !== 'S') {
                continue;
            }

            $label = trim((string) ($submenu['menu_etiqueta'] ?? ''));
            if ($label === '') {
                $label = trim((string) ($submenu['descripcion'] ?? ''));
            }

            $out[] = [
                'label' => $label !== '' ? $label : '—',
                'href'  => self::buildSubmenuHref($mod, $submenu),
            ];
        }

        return $out;
    }

    private static function buildSubmenuHref(array $mod, array $submenu): string
    {
        $rutaSub = trim((string) ($submenu['ruta'] ?? ''));
        if ($rutaSub === '') {
            return self::normalizeModuleHref((string) ($mod['ruta'] ?? ''));
        }

        if (str_starts_with($rutaSub, '/dashboard/') || str_starts_with($rutaSub, 'dashboard/')) {
            $path = str_starts_with($rutaSub, '/') ? $rutaSub : '/' . $rutaSub;

            return site_url(ltrim($path, '/'));
        }

        $parent = rtrim((string) ($mod['ruta'] ?? ''), '/');

        return site_url(ltrim($parent . '/' . ltrim($rutaSub, '/'), '/'));
    }

    private static function normalizeModuleHref(string $ruta): string
    {
        $ruta = trim($ruta);
        if ($ruta === '') {
            return site_url('dashboard/menu');
        }
        if (str_starts_with($ruta, 'http')) {
            return $ruta;
        }

        return site_url(ltrim($ruta, '/'));
    }

    private static function sanitizeIcon(string $icon): string
    {
        $icon = preg_replace('/[^a-z0-9\-]/', '', strtolower($icon)) ?: 'circle';
        $allowed = array_keys(config('MenuSidebar')->iconos);

        return in_array($icon, $allowed, true) ? $icon : 'circle';
    }

    public static function featherSvg(string $icon, int $size = 20): string
    {
        $icon = self::sanitizeIcon($icon);
        $icons = [
            'home'           => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>',
            'message-circle' => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>',
            'calendar'       => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
            'users'          => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
            'clipboard'      => '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>',
            'file-text'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>',
            'tag'            => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line>',
            'dollar-sign'    => '<line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>',
            'settings'       => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>',
            'user'           => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
            'briefcase'      => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>',
            'circle'         => '<circle cx="12" cy="12" r="10"></circle>',
        ];

        $body = $icons[$icon] ?? $icons['circle'];

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-' . esc($icon, 'attr') . '">' . $body . '</svg>';
    }
}
