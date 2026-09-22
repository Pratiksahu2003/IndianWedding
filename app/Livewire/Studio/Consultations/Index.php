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
    public string $date;
    public string $start_time = '11:00';
    public string $end_time = '12:00';

    public function mount(): void
    {
        $this->date = now()->addDay()->toDateString();
    }

    public function addSlot(): void
    {
        $this->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
        ]);
        ConsultationSlot::query()->create([
            'organization_id' => Tenant::id(),
            'staff_user_id' => auth()->id(),
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'timezone' => auth()->user()->timezone ?? 'Asia/Kolkata',
            'is_available' => true,
        ]);
        session()->flash('status', 'Slot opened.');
    }

    public function render()
    {
        return view('livewire.studio.consultations.index', [
            'slots' => ConsultationSlot::query()->with('staff')->orderBy('date')->get(),
            'consultations' => Consultation::query()->orderByDesc('starts_at')->get(),
        ]);
    }
}
