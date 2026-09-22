<?php

namespace App\Http\Controllers\Site;

use App\Actions\CaptureLead;
use App\Http\Controllers\Controller;
use App\Http\Requests\InquiryRequest;
use App\Models\Organization;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    public function store(InquiryRequest $request, CaptureLead $action): RedirectResponse
    {
        $org = Organization::query()->where('slug', config('lumina.public_studio_slug', 'lumina-atelier'))->first()
            ?? Organization::query()->where('is_active', true)->firstOrFail();

        Tenant::set($org->id);

        $action->handle($org, $request->validated() + [
            'source' => 'website',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('inquiry_success', true);
    }
}
