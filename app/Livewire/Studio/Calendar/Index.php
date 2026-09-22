<?php

namespace App\Livewire\Studio\Calendar;

use App\Models\Consultation;
use App\Models\ProjectEvent;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Calendar')]
class Index extends Component
{
    public string $month;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function render()
    {
        $start = Carbon::parse($this->month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $events = ProjectEvent::query()->with('project')->whereBetween('date', [$start, $end])->get();
        $consults = Consultation::query()->whereBetween('starts_at', [$start, $end])->get();

        return view('livewire.studio.calendar.index', compact('start', 'end', 'events', 'consults'));
    }
}
