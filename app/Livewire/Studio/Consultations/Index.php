<?php

namespace App\Livewire\Studio\Consultations;

use App\Models\Consultation;
use App\Models\ConsultationSlot;
use App\Support\Tenant;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Consultations')]
class Index extends Component
{
    public string $date;

    public string $start_time = '11:00';

    public string $end_time = '12:00';

    public function mount(): void
    {
        $this->date = now()->addDay()->toDateString();
    }

    public function addSlot(): void
    {
        abort_unless(
            auth()->user()?->canInOrganization('consultations.manage', Tenant::current())
                || auth()->user()?->hasFullStudioAccess(),
            403,
        );

        $this->validate([
            'date' => ['required', 'date'],
            'start_time' => [
                'required',
                Rule::unique('consultation_slots', 'start_time')
                    ->where(fn ($q) => $q
                        ->where('organization_id', Tenant::requireId())
                        ->where('staff_user_id', auth()->id())
                        ->whereDate('date', $this->date)),
            ],
            'end_time' => ['required', 'after:start_time'],
        ], [
            'start_time.unique' => 'You already have a slot at this date and time.',
        ]);

        try {
            ConsultationSlot::query()->create([
                'organization_id' => Tenant::requireId(),
                'staff_user_id' => auth()->id(),
                'date' => $this->date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'timezone' => auth()->user()->timezone ?? 'Asia/Kolkata',
                'is_available' => true,
            ]);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'start_time' => 'You already have a slot at this date and time.',
            ]);
        }

        session()->flash('status', 'Slot opened.');
    }

    public function deleteSlot(int $id): void
    {
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

    public function render()
    {
        $user = auth()->user();
        $canManage = $user?->canInOrganization('consultations.manage', Tenant::current())
            || $user?->hasFullStudioAccess();

        return view('livewire.studio.consultations.index', [
            'slots' => ConsultationSlot::query()->with('staff')->orderBy('date')->get(),
            'consultations' => Consultation::query()->orderByDesc('starts_at')->get(),
            'canCreate' => (bool) $canManage,
            'canDelete' => (bool) ($user?->canDeleteInOrganization(Tenant::current()) && $canManage),
        ]);
    }
}
