<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Organization;
use App\Models\Package;
use App\Models\PortfolioItem;
use App\Models\Testimonial;
use App\Support\Tenant;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function home(): View
    {
        $org = $this->studio();

        return view('public.home', [
            'organization' => $org,
            'packages' => $org ? Package::withoutTenant()->where('organization_id', $org->id)->where('is_public', true)->where('is_active', true)->orderBy('sort_order')->get() : collect(),
            'testimonials' => $org ? Testimonial::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->latest()->limit(6)->get() : collect(),
            'portfolio' => $org ? PortfolioItem::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->orderBy('sort_order')->limit(8)->get() : collect(),
        ]);
    }

    public function page(string $page): View
    {
        $org = $this->studio();
        abort_unless(in_array($page, ['about', 'services', 'packages', 'portfolio', 'gallery', 'testimonials', 'faq', 'contact', 'book-consultation'], true), 404);

        return view('public.'.$page, [
            'organization' => $org,
            'packages' => $org ? Package::withoutTenant()->where('organization_id', $org->id)->where('is_public', true)->where('is_active', true)->orderBy('sort_order')->get() : collect(),
            'testimonials' => $org ? Testimonial::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->latest()->get() : collect(),
            'portfolio' => $org ? PortfolioItem::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->orderBy('sort_order')->get() : collect(),
            'faqs' => $org ? Faq::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->orderBy('sort_order')->get() : collect(),
        ]);
    }

    protected function studio(): ?Organization
    {
        $org = Organization::query()->where('slug', config('lumina.public_studio_slug', 'lumina-atelier'))->first()
            ?? Organization::query()->where('is_active', true)->first();

        if ($org) {
            Tenant::set($org->id);
        }

        return $org;
    }
}
