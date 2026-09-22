<?php

namespace App\Http\Controllers\Site;

use App\Actions\BookConsultation;
use App\Http\Controllers\Controller;
use App\Models\ConsultationSlot;
use App\Models\Organization;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function store(Request $request, BookConsultation $action): RedirectResponse
    {
        $data = $request->validate([
            'slot_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'string', 'max:0'],
        ]);

        $org = Organization::query()->where('is_active', true)->firstOrFail();
        Tenant::set($org->id);

        $slot = ConsultationSlot::query()
            ->where('organization_id', $org->id)
            ->whereKey($data['slot_id'])
            ->firstOrFail();

        $action->handle($slot, $data);

        return back()->with('consultation_success', true);
    }
}
