<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Seo extends BaseConfig
{
    public string $siteName = 'NutriNext';

    public string $siteTagline = 'Gestión integral para nutricionistas';

    public string $defaultTitle = 'NutriNext - Software de gestión para nutricionistas en Chile';

    public string $defaultDescription = 'Agenda de citas online, historial clínico, planes alimentarios, pagos y recordatorios por WhatsApp. Plataforma para consultas nutricionales en Chile.';

    public string $defaultKeywords = 'nutrinext, nutrición, nutricionista, software nutrición, agenda citas, consulta nutricional, plan alimentario, Chile';

    public string $ogImage = 'lib/logo/logo.png';

    public string $locale = 'es_CL';

    public string $twitterHandle = '';

    /**
     * Páginas estáticas indexables NutriNext (sin rutas legacy MANSANCHEZ:
     * servicios, proyectos, galería, nosotros antiguo, etc.).
     */
    public array $staticPages = [
        ''                      => ['priority' => '1.0',  'changefreq' => 'weekly'],
        'funcionalidades'       => ['priority' => '0.9',  'changefreq' => 'weekly'],
        'precios'               => ['priority' => '0.92', 'changefreq' => 'weekly'],
        'equipo'                => ['priority' => '0.85', 'changefreq' => 'monthly'],
        'reservar'              => ['priority' => '0.95', 'changefreq' => 'weekly'],
        'contacto'              => ['priority' => '0.8',  'changefreq' => 'monthly'],
        'politica-privacidad'   => ['priority' => '0.3',  'changefreq' => 'yearly'],
        'terminos-condiciones'  => ['priority' => '0.3',  'changefreq' => 'yearly'],
    ];
}
