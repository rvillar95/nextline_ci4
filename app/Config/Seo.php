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

    /** Páginas estáticas indexables (ruta relativa sin barra inicial). */
    public array $staticPages = [
        '' => ['priority' => '1.0', 'changefreq' => 'weekly'],
        'reservar' => ['priority' => '0.95', 'changefreq' => 'weekly'],
        'precios' => ['priority' => '0.92', 'changefreq' => 'weekly'],
        'funcionalidades' => ['priority' => '0.9', 'changefreq' => 'weekly'],
        'equipo' => ['priority' => '0.85', 'changefreq' => 'monthly'],
        'nosotros' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'servicios' => ['priority' => '0.85', 'changefreq' => 'weekly'],
        'servicios-categorias' => ['priority' => '0.75', 'changefreq' => 'weekly'],
        'proyectos' => ['priority' => '0.7', 'changefreq' => 'weekly'],
        'galeria' => ['priority' => '0.7', 'changefreq' => 'weekly'],
        'galeria-categorias' => ['priority' => '0.65', 'changefreq' => 'weekly'],
        'contacto' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'politica-privacidad' => ['priority' => '0.3', 'changefreq' => 'yearly'],
        'terminos-condiciones' => ['priority' => '0.3', 'changefreq' => 'yearly'],
    ];
}
