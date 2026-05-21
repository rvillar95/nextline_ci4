# robots.txt - <?= esc($siteUrl) ?>


User-agent: *
Allow: /

Disallow: /writable/
Disallow: /vendor/
Disallow: /app/
Disallow: /system/
Disallow: /tests/
Disallow: /dashboard/
Disallow: /login
Disallow: /confirmar-cita
Disallow: /cancelar-cita
Disallow: /gracias
Disallow: /nosotros
Disallow: /servicios
Disallow: /servicios-categorias
Disallow: /proyectos
Disallow: /galeria
Disallow: /galeria-categorias
Disallow: /reservar/disponibilidad
Disallow: /reservar/paciente-por-rut
Disallow: /whatsapp/
Disallow: /test-email

Allow: /lib/
Allow: /uploads/

Sitemap: <?= esc($siteUrl) ?>/sitemap.xml

User-agent: Googlebot
Allow: /

User-agent: Bingbot
Allow: /

User-agent: AhrefsBot
Disallow: /

User-agent: SemrushBot
Disallow: /

User-agent: MJ12bot
Disallow: /

User-agent: DotBot
Disallow: /
