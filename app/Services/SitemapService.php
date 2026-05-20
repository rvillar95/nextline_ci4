<?php

namespace App\Services;

use Config\Seo;

class SitemapService
{
    protected Seo $config;

    public function __construct()
    {
        $this->config = config('Seo');
    }

    /**
     * @return list<array{loc: string, lastmod: string, changefreq: string, priority: string}>
     */
    public function getUrls(): array
    {
        helper('seo');

        $base = seo_site_base_url();
        $today = date('Y-m-d');
        $urls = [];

        foreach ($this->config->staticPages as $path => $meta) {
            $urls[] = $this->entry(
                $base . ($path === '' ? '/' : '/' . $path),
                $today,
                $meta['changefreq'],
                $meta['priority']
            );
        }

        $db = \Config\Database::connect();

        $this->appendSlugUrls($urls, $db, 'servicio', $base . '/servicios/', 'monthly', '0.8');
        $this->appendSlugUrls($urls, $db, 'servicio_categoria', $base . '/servicios-categorias/', 'monthly', '0.7');
        $this->appendSlugUrls($urls, $db, 'proyectos', $base . '/proyectos/', 'monthly', '0.75', 'estado_publico');

        $this->appendUrlsSafely($urls, function () use ($db, $base) {
            $rows = $db->table('servicio_nutrinext')
                ->select('codigo, factualizacion')
                ->where('estado', 'A')
                ->where('visible_web', 'S')
                ->where('codigo IS NOT NULL')
                ->where('codigo !=', '')
                ->get()
                ->getResult();
            $entries = [];
            foreach ($rows as $row) {
                $entries[] = $this->entry(
                    $base . '/funcionalidades/' . rawurlencode($row->codigo),
                    $this->formatDate($row->factualizacion ?? null),
                    'monthly',
                    '0.85'
                );
            }

            return $entries;
        });

        $this->appendUrlsSafely($urls, function () use ($db, $base) {
            $rows = $db->table('galeria')
                ->select('id, factualizacion')
                ->where('estado', 'A')
                ->get()
                ->getResult();
            $entries = [];
            foreach ($rows as $row) {
                $entries[] = $this->entry(
                    $base . '/galeria/detalle/' . (int) $row->id,
                    $this->formatDate($row->factualizacion ?? null),
                    'monthly',
                    '0.6'
                );
            }

            return $entries;
        });

        $this->appendUrlsSafely($urls, function () use ($db, $base) {
            $rows = $db->table('galeria_categoria')
                ->select('slug, factualizacion')
                ->where('estado', 'A')
                ->where('slug IS NOT NULL')
                ->where('slug !=', '')
                ->get()
                ->getResult();
            $entries = [];
            foreach ($rows as $row) {
                $slug = rawurlencode($row->slug);
                $lastmod = $this->formatDate($row->factualizacion ?? null);
                $entries[] = $this->entry($base . '/galeria-categorias/' . $slug, $lastmod, 'monthly', '0.65');
                $entries[] = $this->entry($base . '/galeria/categoria/' . $slug, $lastmod, 'monthly', '0.65');
            }

            return $entries;
        });

        return $urls;
    }

    public function toXml(): string
    {
        $urls = $this->getUrls();

        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($urls as $url) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>' . $this->xmlEscape($url['loc']) . '</loc>';
            $lines[] = '    <lastmod>' . $this->xmlEscape($url['lastmod']) . '</lastmod>';
            $lines[] = '    <changefreq>' . $this->xmlEscape($url['changefreq']) . '</changefreq>';
            $lines[] = '    <priority>' . $this->xmlEscape($url['priority']) . '</priority>';
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines) . "\n";
    }

    /**
     * @param list<array{loc: string, lastmod: string, changefreq: string, priority: string}> $urls
     */
    protected function appendSlugUrls(
        array &$urls,
        $db,
        string $table,
        string $urlPrefix,
        string $changefreq,
        string $priority,
        string $estadoColumn = 'estado'
    ): void {
        $this->appendUrlsSafely($urls, function () use ($db, $table, $urlPrefix, $changefreq, $priority, $estadoColumn) {
            $builder = $db->table($table)
                ->select('slug, factualizacion')
                ->where($estadoColumn, 'A')
                ->where('slug IS NOT NULL')
                ->where('slug !=', '');

            if ($table === 'proyectos') {
                $builder->where('feliminacion', null);
            }

            $rows = $builder->get()->getResult();
            $entries = [];
            foreach ($rows as $row) {
                $entries[] = $this->entry(
                    $urlPrefix . rawurlencode($row->slug),
                    $this->formatDate($row->factualizacion ?? null),
                    $changefreq,
                    $priority
                );
            }

            return $entries;
        });
    }

    /**
     * @param list<array{loc: string, lastmod: string, changefreq: string, priority: string}> $urls
     * @param callable(): list<array{loc: string, lastmod: string, changefreq: string, priority: string}> $callback
     */
    protected function appendUrlsSafely(array &$urls, callable $callback): void
    {
        try {
            $urls = array_merge($urls, $callback());
        } catch (\Throwable $e) {
            log_message('warning', 'Sitemap: omitiendo bloque de URLs: ' . $e->getMessage());
        }
    }

    protected function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    /**
     * @return array{loc: string, lastmod: string, changefreq: string, priority: string}
     */
    protected function entry(string $loc, string $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc'        => $loc,
            'lastmod'    => $lastmod,
            'changefreq' => $changefreq,
            'priority'   => $priority,
        ];
    }

    protected function formatDate($value): string
    {
        if (empty($value)) {
            return date('Y-m-d');
        }

        $ts = is_numeric($value) ? (int) $value : strtotime((string) $value);

        return $ts ? date('Y-m-d', $ts) : date('Y-m-d');
    }
}
