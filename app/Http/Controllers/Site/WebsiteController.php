<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Organization;
use App\Models\Package;
use App\Models\PortfolioItem;
use App\Models\Testimonial;
use App\Support\Tenant;
use Illuminate\Support\Str;
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
            'portfolio' => $org ? PortfolioItem::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->orderBy('sort_order')->limit(12)->get() : collect(),
        ]);
    }

    public function page(string $page): View
    {
        $org = $this->studio();
        abort_unless(in_array($page, ['about', 'services', 'packages', 'portfolio', 'gallery', 'testimonials', 'faq', 'contact', 'book-consultation', 'our-team'], true), 404);

        return view('public.'.$page, [
            'organization' => $org,
            'packages' => $org ? Package::withoutTenant()->with('items')->where('organization_id', $org->id)->where('is_public', true)->where('is_active', true)->orderBy('sort_order')->get() : collect(),
            'testimonials' => $org ? Testimonial::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->latest()->get() : collect(),
            'portfolio' => $org ? PortfolioItem::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->orderBy('sort_order')->get() : collect(),
            'faqs' => $org ? Faq::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->orderBy('sort_order')->get() : collect(),
            'team' => $org ? \App\Models\TeamMember::withoutTenant()->where('organization_id', $org->id)->where('is_published', true)->orderBy('sort_order')->get() : collect(),
        ]);
    }

    public function service(string $slug): View
    {
        $org = $this->studio();
        abort_unless($org, 404);

        $package = Package::withoutTenant()
            ->with('items')
            ->where('organization_id', $org->id)
            ->where('slug', $slug)
            ->where('is_public', true)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Package::withoutTenant()
            ->where('organization_id', $org->id)
            ->where('is_public', true)
            ->where('is_active', true)
            ->where('id', '!=', $package->id)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        $gallery = PortfolioItem::withoutTenant()
            ->where('organization_id', $org->id)
            ->where('is_published', true)
            ->when($package->slug, function ($query) use ($package) {
                $query->where(function ($inner) use ($package) {
                    $inner->where('category', $package->slug)
                        ->orWhere('category', str_replace('-shoot', '', $package->slug))
                        ->orWhere('category', 'like', '%'.Str::before($package->slug, '-').'%');
                });
            })
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        if ($gallery->isEmpty()) {
            $gallery = PortfolioItem::withoutTenant()
                ->where('organization_id', $org->id)
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->limit(6)
                ->get();
        }

        return view('public.service-show', [
            'organization' => $org,
            'package' => $package,
            'related' => $related,
            'gallery' => $gallery,
            'title' => $package->name.' — Unik Studio',
            'description' => Str::limit(strip_tags((string) $package->description), 160),
        ]);
    }

    public function project(string $slug): View
    {
        $org = $this->studio();
        abort_unless($org, 404);

        $item = PortfolioItem::withoutTenant()
            ->where('organization_id', $org->id)
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $related = PortfolioItem::withoutTenant()
            ->where('organization_id', $org->id)
            ->where('is_published', true)
            ->where('id', '!=', $item->id)
            ->when($item->category, fn ($q) => $q->where('category', $item->category))
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        if ($related->count() < 3) {
            $related = PortfolioItem::withoutTenant()
                ->where('organization_id', $org->id)
                ->where('is_published', true)
                ->where('id', '!=', $item->id)
                ->orderBy('sort_order')
                ->limit(6)
                ->get();
        }

        $services = Package::withoutTenant()
            ->where('organization_id', $org->id)
            ->where('is_public', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        return view('public.project-show', [
            'organization' => $org,
            'item' => $item,
            'related' => $related,
            'services' => $services,
            'title' => $item->title.' — Unik Studio',
            'description' => Str::limit(strip_tags((string) ($item->story ?: $item->title)), 160),
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
