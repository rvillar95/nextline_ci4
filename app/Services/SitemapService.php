<?php

namespace App\Services;

use App\Models\Usuario;
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

        // Detalle de funcionalidades (catálogo NutriNext)
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

        // Fichas públicas del equipo
        $this->appendUrlsSafely($urls, function () use ($db, $base) {
            $rows = $db->table('usuario')
                ->select('id, factualizacion')
                ->where('perfil_id', Usuario::PERFIL_NUTRICIONISTA)
                ->where('estado', 'A')
                ->get()
                ->getResult();
            $entries = [];
            foreach ($rows as $row) {
                $entries[] = $this->entry(
                    $base . '/equipo/' . (int) $row->id,
                    $this->formatDate($row->factualizacion ?? null),
                    'monthly',
                    '0.75'
                );
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
