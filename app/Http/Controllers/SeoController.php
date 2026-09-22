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
            'Disallow: /login',
            'Disallow: /api',
            'Sitemap: '.url('/sitemap.xml'),
            '',
        ]);

        return response($body, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    }

    public function sitemap(): Response
    {
        $org = \App\Models\Organization::query()->where('is_active', true)->first();
        $paths = ['/', '/about', '/our-team', '/services', '/packages', '/portfolio', '/gallery', '/testimonials', '/faq', '/contact', '/book-consultation'];
        if ($org) {
            $paths = array_merge(
                $paths,
                \App\Models\Package::withoutTenant()->where('organization_id', $org->id)->where('is_public', true)->where('is_active', true)->pluck('slug')->map(fn ($slug) => '/services/'.$slug)->all(),
                \App\Models\PortfolioItem::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->whereNotNull('slug')->pluck('slug')->map(fn ($slug) => '/projects/'.$slug)->all(),
            );
        }
        $urls = collect($paths)->map(function ($path) {
            return '<url><loc>'.e(url($path)).'</loc><changefreq>weekly</changefreq></url>';
        })->implode('');

        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$urls.'</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
