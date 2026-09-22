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
        $this->ensureStaffCanAddToCalendar();

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

        $orgId = Tenant::requireId();
        $startsAt = Carbon::parse($this->date.' '.$this->start_time);
        $endsAt = $this->end_time
            ? Carbon::parse($this->date.' '.$this->end_time)
            : null;

        if ($this->kind === 'consultation_slot') {
            $this->validate([
                'title' => ['nullable', 'string', 'max:180'],
                'notes' => ['nullable', 'string', 'max:2000'],
            ]);

            try {
                ConsultationSlot::query()->create([
                    'organization_id' => $orgId,
                    'staff_user_id' => auth()->id(),
                    'date' => $this->date,
                    'start_time' => ConsultationSlot::normalizeTime($this->start_time),
                    'end_time' => ConsultationSlot::normalizeTime(
                        $this->end_time ?: Carbon::parse($this->start_time)->addHour()->format('H:i')
                    ),
                    'title' => $this->title ?: null,
                    'description' => $this->notes ?: null,
                    'timezone' => auth()->user()->timezone ?? 'Asia/Kolkata',
                    'is_available' => true,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                $this->addError('start_time', 'A consultation slot already exists at this date and time.');

                return;
            }

            session()->flash('status', 'Consultation slot added to the calendar.');

            $this->resetForm();
            $this->dispatchCalendarRefresh();

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
            $this->dispatchCalendarRefresh();

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
        $this->dispatchCalendarRefresh();
    }

    public function deleteSchedule(int $scheduleId): void
    {
        $this->deleteCalendarEvent('schedule', $scheduleId);
    }

    public function deleteCalendarEvent(string $type, int $id): void
    {
        $this->ensureStaffCanViewCalendar();

        match ($type) {
            'schedule' => $this->deleteStudioSchedule($id),
            'project_event' => $this->deleteProjectEvent($id),
            'slot' => $this->deleteConsultationSlot($id),
            default => abort(404),
        };

        session()->flash('status', 'Calendar item removed.');
        $this->dispatchCalendarRefresh();
    }

    protected function deleteStudioSchedule(int $id): void
    {
        $schedule = StudioSchedule::query()->findOrFail($id);
        $this->ensureStaffCanDeleteSchedule($schedule);
        $schedule->delete();
    }

    protected function deleteProjectEvent(int $id): void
    {
        abort_unless(auth()->user()?->canDeleteInOrganization(Tenant::current()), 403);
        ProjectEvent::query()->findOrFail($id)->delete();
    }

    protected function deleteConsultationSlot(int $id): void
    {
        abort_unless(auth()->user()?->canDeleteInOrganization(Tenant::current()), 403);
        ConsultationSlot::query()->findOrFail($id)->delete();
    }

    public function updatedMonth(): void
    {
        $this->dispatchCalendarRefresh();
    }

    protected function resetForm(): void
    {
        $this->reset('title', 'notes', 'project_id');
        $this->kind = 'general';
        $this->event_type = 'wedding';
        $this->start_time = '10:00';
        $this->end_time = '11:00';
    }

    protected function ensureStaffCanViewCalendar(): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        // Ensure single-studio context is bound even without session org id.
        if (! Tenant::id()) {
            Tenant::set(Tenant::soleOrganizationId());
        }

        abort_unless(
            $user->hasFullStudioAccess()
            || $user->canInOrganization('calendar.manage')
            || $user->canInOrganization('calendar.view')
            || $user->roleIn()?->isStaff(),
            403,
        );
    }

    protected function ensureStaffCanAddToCalendar(): void
    {
        $this->ensureStaffCanViewCalendar();
    }

    protected function ensureStaffCanDeleteSchedule(StudioSchedule $schedule): void
    {
        $this->ensureStaffCanViewCalendar();
        abort_unless(auth()->user()?->canDeleteInOrganization(Tenant::current()), 403);
    }

    protected function dispatchCalendarRefresh(): void
    {
        [$start, $end] = $this->visibleRange();

        $this->dispatch(
            'calendar-refreshed',
            events: $this->buildCalendarEvents($this->fetchEvents($start, $end)),
            month: $this->month,
        );
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function visibleRange(): array
    {
        $center = Carbon::parse($this->month.'-01');

        return [
            $center->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY),
            $center->copy()->endOfMonth()->endOfWeek(Carbon::MONDAY),
        ];
    }

    /**
     * @return array{
     *     events: Collection,
     *     consults: Collection,
     *     slots: Collection,
     *     schedules: Collection,
     *     followUps: Collection,
     * }
     */
    protected function fetchEvents(Carbon $start, Carbon $end): array
    {
        return [
            'events' => ProjectEvent::query()->with('project')->whereBetween('date', [$start, $end])->orderBy('date')->get(),
            'consults' => Consultation::query()->whereBetween('starts_at', [$start, $end])->orderBy('starts_at')->get(),
            'slots' => ConsultationSlot::query()->with('staff')->whereBetween('date', [$start, $end])->orderBy('date')->get(),
            'schedules' => StudioSchedule::query()->with(['creator', 'project'])->whereBetween('starts_at', [$start, $end])->orderBy('starts_at')->get(),
            'followUps' => Lead::query()
                ->whereNotNull('follow_up_at')
                ->whereBetween('follow_up_at', [$start, $end])
                ->orderBy('follow_up_at')
                ->get(['id', 'name', 'lead_number', 'follow_up_at']),
        ];
    }

    /**
     * @param  array{
     *     events: Collection,
     *     consults: Collection,
     *     slots: Collection,
     *     schedules: Collection,
     *     followUps: Collection,
     * }  $data
     * @return list<array<string, mixed>>
     */
    protected function buildCalendarEvents(array $data): array
    {
        $items = [];
        $canDeleteAny = auth()->user()?->canDeleteInOrganization(Tenant::current()) ?? false;
        $userId = auth()->id();

        foreach ($data['events'] as $event) {
            $date = $event->date?->format('Y-m-d') ?? now()->toDateString();
            $start = Carbon::parse($date.' '.($event->start_time ?? '09:00'));
            $end = $event->end_time
                ? Carbon::parse($date.' '.$event->end_time)
                : $start->copy()->addHours(2);

            $items[] = [
                'id' => 'project-event-'.$event->id,
                'title' => $event->title,
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'allDay' => blank($event->start_time),
                'type' => 'Project',
                'meta' => ucfirst($event->type->value).' · '.($event->project?->title ?? 'Project'),
                'url' => $event->project ? route('app.projects.show', $event->project) : null,
                'deletable' => $canDeleteAny,
                'recordType' => 'project_event',
                'recordId' => $event->id,
                'scheduleId' => null,
            ];
        }

        foreach ($data['consults'] as $consult) {
            $items[] = [
                'id' => 'consultation-'.$consult->id,
                'title' => $consult->name,
                'start' => $consult->starts_at->toIso8601String(),
                'end' => $consult->starts_at->copy()->addHour()->toIso8601String(),
                'allDay' => false,
                'type' => 'Consultation',
                'meta' => 'Client consultation',
                'url' => route('app.consultations.index'),
                'deletable' => false,
                'scheduleId' => null,
            ];
        }

        foreach ($data['slots'] as $slot) {
            $start = Carbon::parse($slot->date->format('Y-m-d').' '.$slot->start_time);
            $end = Carbon::parse($slot->date->format('Y-m-d').' '.$slot->end_time);

            $items[] = [
                'id' => 'slot-'.$slot->id,
                'title' => $slot->title ?: 'Consultation slot',
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'allDay' => false,
                'type' => 'Availability',
                'meta' => collect([
                    $slot->formattedStart().' – '.$slot->formattedEnd(),
                    $slot->staff?->name,
                    $slot->description ? \Illuminate\Support\Str::limit($slot->description, 40) : null,
                ])->filter()->implode(' · '),
                'url' => route('app.consultations.edit', $slot),
                'deletable' => $canDeleteAny,
                'recordType' => 'slot',
                'recordId' => $slot->id,
                'scheduleId' => null,
            ];
        }

        foreach ($data['schedules'] as $schedule) {
            $items[] = [
                'id' => 'schedule-'.$schedule->id,
                'title' => $schedule->title,
                'start' => $schedule->starts_at->toIso8601String(),
                'end' => ($schedule->ends_at ?? $schedule->starts_at->copy()->addHour())->toIso8601String(),
                'allDay' => false,
                'type' => 'Schedule',
                'meta' => trim(collect([
                    $schedule->starts_at->format('M j · g:i A'),
                    $schedule->project?->title,
                    $schedule->creator?->name,
                ])->filter()->implode(' · ')),
                'url' => $schedule->project ? route('app.projects.show', $schedule->project) : null,
                'deletable' => $canDeleteAny,
                'recordType' => 'schedule',
                'recordId' => $schedule->id,
                'scheduleId' => $schedule->id,
            ];
        }

        foreach ($data['followUps'] as $lead) {
            $items[] = [
                'id' => 'lead-'.$lead->id,
                'title' => 'Follow up: '.$lead->name,
                'start' => $lead->follow_up_at->toIso8601String(),
                'end' => $lead->follow_up_at->copy()->addHour()->toIso8601String(),
                'allDay' => false,
                'type' => 'Lead',
                'meta' => $lead->lead_number,
                'url' => route('app.leads.show', $lead),
                'deletable' => false,
                'scheduleId' => null,
            ];
        }

        return $items;
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
        $canDeleteAny = auth()->user()?->canDeleteInOrganization(Tenant::current()) ?? false;
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
                'label' => $slot->title ?: 'Consultation slot',
                'meta' => collect([
                    $slot->formattedStart().' – '.$slot->formattedEnd(),
                    $slot->staff?->name,
                    $slot->description ? \Illuminate\Support\Str::limit($slot->description, 40) : null,
                ])->filter()->implode(' · '),
                'badge' => 'Availability',
                'href' => route('app.consultations.edit', $slot),
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
                'deletable' => $canDeleteAny,
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

    public function render()
    {
        $this->ensureStaffCanViewCalendar();

        [$start, $end] = $this->visibleRange();
        $data = $this->fetchEvents($start, $end);

        $projects = Project::query()->orderByDesc('wedding_date')->limit(100)->get(['id', 'title', 'project_number']);

        $timeline = $this->buildTimeline(
            $data['events'],
            $data['consults'],
            $data['slots'],
            $data['schedules'],
            $data['followUps'],
        );

        $calendarEvents = $this->buildCalendarEvents($data);
        $role = auth()->user()?->roleIn(Tenant::current());

        return view('livewire.studio.calendar.index', [
            'start' => Carbon::parse($this->month.'-01')->startOfMonth(),
            'end' => Carbon::parse($this->month.'-01')->endOfMonth(),
            'events' => $data['events'],
            'consults' => $data['consults'],
            'consultationSlots' => $data['slots'],
            'schedules' => $data['schedules'],
            'followUps' => $data['followUps'],
            'timeline' => $timeline,
            'projects' => $projects,
            'eventTypes' => EventType::cases(),
            'calendarEvents' => $calendarEvents,
            'canAdd' => $role?->isStaff() ?? false,
            'canManage' => auth()->user()?->canInOrganization('calendar.manage', Tenant::current()),
        ]);
    }
}
