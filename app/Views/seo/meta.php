<?php
helper('seo');
$seoConfig = config('Seo');

$title       = $title ?? $seoConfig->defaultTitle;
$description = $description ?? $seoConfig->defaultDescription;
$keywords    = $keywords ?? $seoConfig->defaultKeywords;
$canonical   = $canonical ?? seo_canonical_url();
$robots      = $robots ?? 'index, follow';
$ogImage     = $og_image ?? base_url($seoConfig->ogImage);
$ogType      = $og_type ?? 'website';
$jsonLdExtra = $json_ld ?? [];

$siteName = $seoConfig->siteName;
$siteBase = seo_site_base_url();

$organizationLd = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => $siteName,
    'url'      => $siteBase . '/',
    'logo'     => base_url($seoConfig->ogImage),
    'description' => $seoConfig->defaultDescription,
];

$websiteLd = [
    '@context' => 'https://schema.org',
    '@type'    => 'WebSite',
    'name'     => $siteName,
    'url'      => $siteBase . '/',
    'description' => $seoConfig->defaultDescription,
    'inLanguage' => 'es-CL',
    'potentialAction' => [
        '@type'  => 'ReserveAction',
        'target' => [
            '@type'       => 'EntryPoint',
            'urlTemplate' => $siteBase . '/reservar',
        ],
        'name' => 'Reservar hora de consulta nutricional',
    ],
];

$jsonLdBlocks = array_merge([$organizationLd, $websiteLd], $jsonLdExtra);
?>
    <title><?= esc($title) ?></title>
    <meta name="description" content="<?= esc($description) ?>" />
    <meta name="keywords" content="<?= esc($keywords) ?>" />
    <meta name="author" content="<?= esc($siteName) ?>" />
    <meta name="robots" content="<?= esc($robots) ?>" />
    <link rel="canonical" href="<?= esc($canonical) ?>" />

    <meta property="og:type" content="<?= esc($ogType) ?>" />
    <meta property="og:url" content="<?= esc($canonical) ?>" />
    <meta property="og:title" content="<?= esc($title) ?>" />
    <meta property="og:description" content="<?= esc($description) ?>" />
    <meta property="og:image" content="<?= esc($ogImage) ?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:site_name" content="<?= esc($siteName) ?>" />
    <meta property="og:locale" content="<?= esc($seoConfig->locale) ?>" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="<?= esc($canonical) ?>" />
    <meta name="twitter:title" content="<?= esc($title) ?>" />
    <meta name="twitter:description" content="<?= esc($description) ?>" />
    <meta name="twitter:image" content="<?= esc($ogImage) ?>" />
<?php if ($seoConfig->twitterHandle !== ''): ?>
    <meta name="twitter:site" content="<?= esc($seoConfig->twitterHandle) ?>" />
<?php endif; ?>

<?php foreach ($jsonLdBlocks as $block): ?>
    <script type="application/ld+json"><?= json_encode($block, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<?php endforeach; ?>
