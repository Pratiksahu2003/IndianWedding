<?php

namespace App\Livewire\Studio\Consultations;

use App\Models\Consultation;
use App\Models\ConsultationSlot;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Consultations')]
class Index extends Component
{
    public function deleteSlot(int $id): void
    {
        abort_unless(
            auth()->user()?->canInOrganization('consultations.manage', Tenant::current())
                || auth()->user()?->hasFullStudioAccess(),
            403,
        );
        abort_unless(auth()->user()?->canDeleteInOrganization(Tenant::current()), 403);

        ConsultationSlot::query()->findOrFail($id)->delete();
        session()->flash('status', 'Slot removed.');
    }

    public function deleteConsultation(int $id): void
    {
        abort_unless(auth()->user()?->canDeleteInOrganization(Tenant::current()), 403);

        Consultation::query()->findOrFail($id)->delete();
        session()->flash('status', 'Consultation removed.');
    }

    public function toggleAvailability(int $id): void
    {
        abort_unless(
            auth()->user()?->canInOrganization('consultations.manage', Tenant::current())
                || auth()->user()?->hasFullStudioAccess(),
            403,
        );

        $slot = ConsultationSlot::query()->findOrFail($id);
        $slot->update(['is_available' => ! $slot->is_available]);
        session()->flash('status', $slot->is_available ? 'Slot marked available.' : 'Slot marked unavailable.');
    }

    public function render()
    {
        $user = auth()->user();
        $canManage = $user?->canInOrganization('consultations.manage', Tenant::current())
            || $user?->hasFullStudioAccess();

        $slots = ConsultationSlot::query()
            ->with('staff')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('livewire.studio.consultations.index', [
            'slots' => $slots,
            'upcomingSlots' => $slots->filter(fn (ConsultationSlot $slot) => $slot->date?->gte(now()->startOfDay())),
            'pastSlots' => $slots->filter(fn (ConsultationSlot $slot) => $slot->date?->lt(now()->startOfDay())),
            'consultations' => Consultation::query()->with('slot')->orderByDesc('starts_at')->limit(50)->get(),
            'canCreate' => (bool) $canManage,
            'canEdit' => (bool) $canManage,
            'canDelete' => (bool) ($user?->canDeleteInOrganization(Tenant::current()) && $canManage),
        ]);
    }
}
