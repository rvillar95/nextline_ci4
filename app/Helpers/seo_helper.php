<?php

/**
 * SEO helpers para vistas públicas.
 */

if (! function_exists('seo_page')) {
    /**
     * Fusiona datos SEO de página con valores por defecto del sitio.
     *
     * @param array<string, mixed> $overrides title, description, keywords, canonical, robots, og_image, json_ld
     *
     * @return array<string, mixed>
     */
    function seo_page(array $overrides = []): array
    {
        $config = config('Seo');

        $defaults = [
            'title'       => $config->defaultTitle,
            'description' => $config->defaultDescription,
            'keywords'    => $config->defaultKeywords,
            'canonical'   => seo_canonical_url(),
            'robots'      => 'index, follow',
            'og_image'    => base_url($config->ogImage),
            'og_type'     => 'website',
            'json_ld'     => [],
        ];

        return array_merge($defaults, $overrides);
    }
}

if (! function_exists('seo_canonical_url')) {
    function seo_canonical_url(?string $path = null): string
    {
        if ($path === null) {
            $uri = service('request')->getUri();
            $path = (string) $uri->getPath();
        }

        $path = trim($path, '/');
        $base = rtrim(config('App')->baseURL, '/');

        return $path === '' ? $base . '/' : $base . '/' . $path;
    }
}

if (! function_exists('seo_site_base_url')) {
    /** URL pública del sitio (producción), sin depender del host de la petición. */
    function seo_site_base_url(): string
    {
        return rtrim(config('App')->baseURL, '/');
    }
}
