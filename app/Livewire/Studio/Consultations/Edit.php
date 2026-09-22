<?php

namespace App\Livewire\Studio\Consultations;

use App\Models\ConsultationSlot;
use App\Support\Tenant;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Edit slot')]
class Edit extends Component
{
    public ConsultationSlot $slot;

    public string $date = '';

    public string $start_time = '11:00';

    public string $end_time = '12:00';

    public string $title = '';

    public string $description = '';

    public bool $is_available = true;

    public function mount(ConsultationSlot $slot): void
    {
        $this->authorizeManage();
        $this->slot = $slot;
        $this->date = $slot->date?->toDateString() ?? now()->toDateString();
        $this->start_time = substr((string) $slot->start_time, 0, 5);
        $this->end_time = substr((string) $slot->end_time, 0, 5);
        $this->title = $slot->title ?? '';
        $this->description = $slot->description ?? '';
        $this->is_available = (bool) $slot->is_available;
    }

    public function save()
    {
        $this->authorizeManage();

        $start = ConsultationSlot::normalizeTime($this->start_time);
        $end = ConsultationSlot::normalizeTime($this->end_time);

        $this->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'title' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_available' => ['boolean'],
        ], [
            'end_time.after' => 'End time must be after start time.',
        ]);

        $duplicate = ConsultationSlot::query()
            ->where('staff_user_id', $this->slot->staff_user_id)
            ->whereDate('date', $this->date)
            ->whereTime('start_time', $start)
            ->where('id', '!=', $this->slot->id)
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'start_time' => 'A slot already exists at this date and time.',
            ]);
        }

        try {
            $this->slot->update([
                'date' => $this->date,
                'start_time' => $start,
                'end_time' => $end,
                'title' => $this->title ?: null,
                'description' => $this->description ?: null,
                'is_available' => $this->is_available,
            ]);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'start_time' => 'A slot already exists at this date and time.',
            ]);
        }

        session()->flash('status', 'Consultation slot updated.');

        return $this->redirect(route('app.consultations.index'), navigate: true);
    }

    protected function authorizeManage(): void
    {
        abort_unless(
            auth()->user()?->canInOrganization('consultations.manage', Tenant::current())
                || auth()->user()?->hasFullStudioAccess(),
            403,
        );
    }

    public function render()
    {
        return view('livewire.studio.consultations.edit', ['isEdit' => true]);
    }
}
