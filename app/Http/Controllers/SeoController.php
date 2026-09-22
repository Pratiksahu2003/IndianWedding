<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $body = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /app',
            'Disallow: /client',
            'Disallow: /platform',
            'Disallow: /login',
            'Disallow: /api',
            'Sitemap: '.url('/sitemap.xml'),
            '',
        ]);

        return response($body, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    }

    public function sitemap(): Response
    {
        $paths = ['/', '/about', '/services', '/packages', '/portfolio', '/gallery', '/testimonials', '/faq', '/contact', '/book-consultation'];
        $urls = collect($paths)->map(function ($path) {
            return '<url><loc>'.e(url($path)).'</loc><changefreq>weekly</changefreq></url>';
        })->implode('');

        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$urls.'</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
