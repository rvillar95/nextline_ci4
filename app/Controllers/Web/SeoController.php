<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Services\SitemapService;

class SeoController extends BaseController
{
    public function sitemap()
    {
        try {
            $xml = (new SitemapService())->toXml();
        } catch (\Throwable $e) {
            log_message('error', 'Sitemap XML: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());

            return $this->response
                ->setStatusCode(500)
                ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->setBody('Error al generar sitemap. Revise writable/logs/.');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->setBody($xml);
    }

    public function robots()
    {
        $base = rtrim(config('App')->baseURL, '/');
        $host = parse_url($base, PHP_URL_HOST) ?: 'nutrinext.cl';
        $scheme = parse_url($base, PHP_URL_SCHEME) ?: 'https';
        $siteUrl = $scheme . '://' . $host;

        $body = view('seo/robots', ['siteUrl' => $siteUrl]);

        return $this->response
            ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->setBody($body);
    }
}
