<?php

namespace App\Livewire\Studio\Calendar;

use App\Enums\EventType;
use App\Models\Consultation;
use App\Models\ConsultationSlot;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectEvent;
use App\Models\StudioSchedule;
use App\Support\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Calendar')]
class Index extends Component
{
    public string $month;

    public string $kind = 'general';

    public string $title = '';

    public string $date = '';

    public string $start_time = '10:00';

    public string $end_time = '11:00';

    public ?int $project_id = null;

    public string $event_type = 'wedding';

    public string $notes = '';

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
        $this->date = now()->toDateString();
    }

    public function addSchedule(): void
    {
        $this->ensureStaffCanManageCalendar();

        $rules = [
            'kind' => ['required', 'in:general,consultation_slot,project_event'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        ];

        if ($this->kind === 'general') {
            $rules['title'] = ['required', 'string', 'max:180'];
        }

        if ($this->kind === 'project_event') {
            $rules['project_id'] = ['required', 'integer', 'exists:projects,id'];
            $rules['event_type'] = ['required', 'string'];
            $rules['title'] = ['nullable', 'string', 'max:180'];
        }

        $this->validate($rules);

        $orgId = Tenant::id();
        $startsAt = Carbon::parse($this->date.' '.$this->start_time);
        $endsAt = $this->end_time
            ? Carbon::parse($this->date.' '.$this->end_time)
            : null;

        if ($this->kind === 'consultation_slot') {
            ConsultationSlot::query()->create([
                'organization_id' => $orgId,
                'staff_user_id' => auth()->id(),
                'date' => $this->date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time ?: Carbon::parse($this->start_time)->addHour()->format('H:i'),
                'timezone' => auth()->user()->timezone ?? 'Asia/Kolkata',
                'is_available' => true,
            ]);

            session()->flash('status', 'Consultation slot added to the calendar.');

            $this->resetForm();

            return;
        }

        if ($this->kind === 'project_event') {
            $project = Project::query()->findOrFail($this->project_id);
            $type = EventType::tryFrom($this->event_type) ?? EventType::Other;
            $title = trim($this->title) !== '' ? $this->title : $type->name;

            ProjectEvent::query()->create([
                'organization_id' => $orgId,
                'project_id' => $project->id,
                'type' => $type,
                'title' => $title,
                'date' => $this->date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'notes' => $this->notes ?: null,
            ]);

            session()->flash('status', 'Project event added to the calendar.');

            $this->resetForm();

            return;
        }

        StudioSchedule::query()->create([
            'organization_id' => $orgId,
            'created_by' => auth()->id(),
            'project_id' => $this->project_id,
            'title' => $this->title,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'notes' => $this->notes ?: null,
        ]);

        session()->flash('status', 'Schedule saved.');

        $this->resetForm();
    }

    public function deleteSchedule(int $scheduleId): void
    {
        $this->ensureStaffCanManageCalendar();

        $schedule = StudioSchedule::query()->findOrFail($scheduleId);
        $schedule->delete();

        session()->flash('status', 'Schedule removed.');
    }

    protected function resetForm(): void
    {
        $this->reset('title', 'notes', 'project_id');
        $this->kind = 'general';
        $this->event_type = 'wedding';
        $this->start_time = '10:00';
        $this->end_time = '11:00';
    }

    protected function ensureStaffCanManageCalendar(): void
    {
        $role = auth()->user()?->roleIn(Tenant::current());
        abort_unless($role?->isStaff(), 403);
        abort_unless(auth()->user()?->canInOrganization('calendar.manage', Tenant::current()), 403);
    }

    public function render()
    {
        $role = auth()->user()?->roleIn(Tenant::current());
        abort_unless($role?->isStaff(), 403);

        $start = Carbon::parse($this->month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $events = ProjectEvent::query()->with('project')->whereBetween('date', [$start, $end])->orderBy('date')->get();
        $consults = Consultation::query()->whereBetween('starts_at', [$start, $end])->orderBy('starts_at')->get();
        $slots = ConsultationSlot::query()->with('staff')->whereBetween('date', [$start, $end])->orderBy('date')->get();
        $schedules = StudioSchedule::query()->with(['creator', 'project'])->whereBetween('starts_at', [$start, $end])->orderBy('starts_at')->get();
        $followUps = Lead::query()
            ->whereNotNull('follow_up_at')
            ->whereBetween('follow_up_at', [$start, $end])
            ->orderBy('follow_up_at')
            ->get(['id', 'name', 'lead_number', 'follow_up_at']);

        $projects = Project::query()->orderByDesc('wedding_date')->limit(100)->get(['id', 'title', 'project_number']);

        $timeline = $this->buildTimeline($events, $consults, $slots, $schedules, $followUps);

        return view('livewire.studio.calendar.index', [
            'start' => $start,
            'end' => $end,
            'events' => $events,
            'consults' => $consults,
            'slots' => $slots,
            'schedules' => $schedules,
            'followUps' => $followUps,
            'timeline' => $timeline,
            'projects' => $projects,
            'eventTypes' => EventType::cases(),
            'canManage' => auth()->user()?->canInOrganization('calendar.manage', Tenant::current()),
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ProjectEvent>  $events
     * @param  \Illuminate\Support\Collection<int, Consultation>  $consults
     * @param  \Illuminate\Support\Collection<int, ConsultationSlot>  $slots
     * @param  \Illuminate\Support\Collection<int, StudioSchedule>  $schedules
     * @param  \Illuminate\Support\Collection<int, Lead>  $followUps
     * @return Collection<int, array{sort: Carbon, label: string, meta: string, badge: string, href: ?string, deletable: bool, id: ?int}>
     */
    protected function buildTimeline(
        Collection $events,
        Collection $consults,
        Collection $slots,
        Collection $schedules,
        Collection $followUps,
    ): Collection {
        $items = collect();

        foreach ($events as $event) {
            $when = $event->date ? Carbon::parse($event->date->format('Y-m-d').' '.($event->start_time ?? '09:00')) : now();
            $items->push([
                'sort' => $when,
                'label' => $event->title,
                'meta' => ucfirst($event->type->value).' · '.($event->project?->title ?? 'Project'),
                'badge' => 'Project',
                'href' => $event->project ? route('app.projects.show', $event->project) : null,
                'deletable' => false,
                'id' => null,
            ]);
        }

        foreach ($consults as $consult) {
            $items->push([
                'sort' => $consult->starts_at,
                'label' => $consult->name,
                'meta' => 'Client consultation',
                'badge' => 'Consultation',
                'href' => route('app.consultations.index'),
                'deletable' => false,
                'id' => null,
            ]);
        }

        foreach ($slots as $slot) {
            $when = Carbon::parse($slot->date->format('Y-m-d').' '.$slot->start_time);
            $items->push([
                'sort' => $when,
                'label' => 'Consultation slot open',
                'meta' => $slot->start_time.' – '.$slot->end_time.' · '.($slot->staff?->name ?? 'Studio'),
                'badge' => 'Availability',
                'href' => route('app.consultations.index'),
                'deletable' => false,
                'id' => null,
            ]);
        }

        foreach ($schedules as $schedule) {
            $items->push([
                'sort' => $schedule->starts_at,
                'label' => $schedule->title,
                'meta' => trim(collect([
                    $schedule->starts_at->format('M j · g:i A'),
                    $schedule->project?->title,
                    $schedule->creator?->name,
                ])->filter()->implode(' · ')),
                'badge' => 'Schedule',
                'href' => $schedule->project ? route('app.projects.show', $schedule->project) : null,
                'deletable' => true,
                'id' => $schedule->id,
            ]);
        }

        foreach ($followUps as $lead) {
            $items->push([
                'sort' => $lead->follow_up_at,
                'label' => 'Follow up: '.$lead->name,
                'meta' => $lead->lead_number,
                'badge' => 'Lead',
                'href' => route('app.leads.show', $lead),
                'deletable' => false,
                'id' => null,
            ]);
        }

        return $items->sortBy('sort')->values();
    }
}
