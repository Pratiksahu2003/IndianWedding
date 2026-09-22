<div
    class="space-y-6"
    x-data="{
        showForm: false,
        view: 'calendar',
    }"
    @open-schedule-form.window="showForm = true"
>

    {{-- Page Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm text-[#16120f]/50">Studio Calendar</p>
            <h1 class="mt-1 font-[Cormorant_Garamond] text-4xl md:text-5xl">{{ $start->format('F Y') }}</h1>
            @if ($canAdd)
            <p class="mt-1 text-xs text-[#16120f]/45">Any team member can add schedules — click a date or use the button below.</p>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- View toggle --}}
            <div class="flex items-center gap-1 rounded-2xl bg-white p-1 shadow-sm ring-1 ring-black/5">
                <button type="button" @click="view='calendar'" :class="view==='calendar' ? 'bg-[#16120f] text-white' : 'text-[#16120f]/60 hover:text-[#16120f]'" class="rounded-xl px-3 py-1.5 text-xs font-medium transition">Calendar</button>
                <button type="button" @click="view='timeline'" :class="view==='timeline' ? 'bg-[#16120f] text-white' : 'text-[#16120f]/60 hover:text-[#16120f]'" class="rounded-xl px-3 py-1.5 text-xs font-medium transition">Timeline</button>
            </div>

            @if ($canAdd)
            <button
                type="button"
                @click="showForm = !showForm"
                id="btn-add-schedule"
                class="inline-flex items-center gap-2 rounded-2xl bg-[#16120f] px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-[#2a2218]"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                Add Schedule
            </button>
            @endif
        </div>
    </div>

    {{-- Flash success --}}
    <x-swal-flash />
{{-- Add Schedule Form --}}
    @if ($canAdd)
    <div x-show="showForm" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
            <h2 class="font-[Cormorant_Garamond] text-2xl">Add Schedule</h2>

            <form wire:submit="addSchedule" class="mt-5 space-y-4">
                <div class="flex gap-2 rounded-2xl bg-[#f6f1ea] p-1">
                    @foreach (['general' => 'General', 'consultation_slot' => 'Consultation Slot', 'project_event' => 'Project Event'] as $val => $lbl)
                    <label class="flex-1">
                        <input type="radio" wire:model.live="kind" value="{{ $val }}" class="sr-only peer" id="kind-{{ $val }}">
                        <span class="block cursor-pointer rounded-xl py-2 text-center text-xs font-medium text-[#16120f]/55 transition peer-checked:bg-white peer-checked:text-[#16120f] peer-checked:shadow-sm">{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>

                @error('kind') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="sched-date" class="block text-xs font-medium text-[#16120f]/55 mb-1">Date</label>
                        <input type="date" wire:model="date" id="sched-date" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                        @error('date') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sched-start" class="block text-xs font-medium text-[#16120f]/55 mb-1">Start time</label>
                        <input type="time" wire:model="start_time" id="sched-start" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                        @error('start_time') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sched-end" class="block text-xs font-medium text-[#16120f]/55 mb-1">End time</label>
                        <input type="time" wire:model="end_time" id="sched-end" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                        @error('end_time') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    @if ($kind !== 'consultation_slot')
                    <div>
                        <label for="sched-title" class="block text-xs font-medium text-[#16120f]/55 mb-1">
                            Title @if ($kind === 'project_event') <span class="text-[#16120f]/30">(optional)</span> @endif
                        </label>
                        <input type="text" wire:model="title" id="sched-title" placeholder="e.g. Pre-wedding shoot" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    @endif
                </div>

                @if ($kind === 'consultation_slot')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="sched-slot-title" class="block text-xs font-medium text-[#16120f]/55 mb-1">Title <span class="text-[#16120f]/30">(optional)</span></label>
                        <input type="text" wire:model="title" id="sched-slot-title" placeholder="e.g. Discovery call" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                    </div>
                    <div>
                        <label for="sched-slot-desc" class="block text-xs font-medium text-[#16120f]/55 mb-1">Description <span class="text-[#16120f]/30">(optional)</span></label>
                        <input type="text" wire:model="notes" id="sched-slot-desc" placeholder="What this slot covers…" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                    </div>
                </div>
                @endif

                @if ($kind === 'project_event')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="sched-project" class="block text-xs font-medium text-[#16120f]/55 mb-1">Project</label>
                        <select wire:model="project_id" id="sched-project" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                            <option value="">Select project…</option>
                            @foreach ($projects as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->project_number }} · {{ $proj->title }}</option>
                            @endforeach
                        </select>
                        @error('project_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="sched-event-type" class="block text-xs font-medium text-[#16120f]/55 mb-1">Event type</label>
                        <select wire:model="event_type" id="sched-event-type" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                            @foreach ($eventTypes as $type)
                            <option value="{{ $type->value }}">{{ ucfirst(str_replace('_', ' ', $type->value)) }}</option>
                            @endforeach
                        </select>
                        @error('event_type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif

                @if ($kind === 'general')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="sched-general-project" class="block text-xs font-medium text-[#16120f]/55 mb-1">Link to project <span class="text-[#16120f]/30">(optional)</span></label>
                        <select wire:model="project_id" id="sched-general-project" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                            <option value="">None</option>
                            @foreach ($projects as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->project_number }} · {{ $proj->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="sched-notes" class="block text-xs font-medium text-[#16120f]/55 mb-1">Notes <span class="text-[#16120f]/30">(optional)</span></label>
                        <input type="text" wire:model="notes" id="sched-notes" placeholder="Any details…" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                    </div>
                </div>
                @endif

                @if ($kind === 'project_event')
                <div>
                    <label for="sched-pe-notes" class="block text-xs font-medium text-[#16120f]/55 mb-1">Notes <span class="text-[#16120f]/30">(optional)</span></label>
                    <input type="text" wire:model="notes" id="sched-pe-notes" placeholder="Any details…" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c4a574]">
                </div>
                @endif

                <div class="flex items-center gap-3 pt-1">
                    <button type="submit" id="btn-save-schedule" class="inline-flex items-center gap-2 rounded-xl bg-[#16120f] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#2a2218]">
                        <span wire:loading.remove wire:target="addSchedule">Save to calendar</span>
                        <span wire:loading wire:target="addSchedule">Saving…</span>
                    </button>
                    <button type="button" @click="showForm=false" class="rounded-xl px-4 py-2.5 text-sm text-[#16120f]/55 transition hover:text-[#16120f]">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- FullCalendar --}}
    <div x-show="view==='calendar'" x-cloak>
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 min-w-0">
                <div
                    wire:ignore
                    x-data="studioCalendar({
                        events: @js($calendarEvents),
                        canAdd: @js($canAdd),
                        month: @js($month),
                    })"
                    x-effect="if (view === 'calendar') { $nextTick(() => instance?.updateSize?.()) }"
                    class="studio-calendar w-full rounded-3xl bg-white p-4 shadow-sm ring-1 ring-black/5 md:p-5"
                >
                    <div x-ref="root" class="w-full"></div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-3xl bg-[#16120f] p-5 text-[#f6f1ea]">
                    <p class="text-[10px] uppercase tracking-[0.16em] text-white/40">{{ $start->format('F Y') }}</p>
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        @foreach ([
                            ['Project events', $events->count(), 'bg-[#e8ddd4]/20 text-[#e8ddd4]'],
                            ['Consultations', $consults->count(), 'bg-[#dde8e4]/20 text-[#dde8e4]'],
                            ['Open slots', $consultationSlots->count(), 'bg-[#dde4e8]/20 text-[#dde4e8]'],
                            ['Schedules', $schedules->count(), 'bg-[#e8e4dd]/20 text-[#e8e4dd]'],
                        ] as [$label, $count, $cls])
                        <div class="rounded-2xl {{ $cls }} p-3">
                            <p class="text-2xl font-semibold">{{ $count }}</p>
                            <p class="mt-1 text-[10px] opacity-70">{{ $label }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#16120f]/45">Legend</p>
                    <div class="mt-3 space-y-2">
                        @foreach ([
                            'Project' => 'bg-[#e8ddd4] text-[#7a5c38]',
                            'Consultation' => 'bg-[#dde8e4] text-[#2e5e52]',
                            'Availability' => 'bg-[#dde4e8] text-[#2e4a5e]',
                            'Schedule' => 'bg-[#e8e4dd] text-[#5e522e]',
                            'Lead' => 'bg-[#e8dde8] text-[#5e2e5e]',
                        ] as $label => $cls)
                        <span class="flex items-center gap-2 text-xs text-[#16120f]/55">
                            <span class="h-2.5 w-2.5 rounded-sm {{ $cls }}"></span>
                            {{ $label }}
                        </span>
                        @endforeach
                    </div>
                </div>

                @if ($canAdd)
                <div class="rounded-3xl bg-[#f6f1ea] p-5">
                    <p class="text-xs font-semibold text-[#16120f]/55">Quick add</p>
                    <p class="mt-2 text-xs leading-relaxed text-[#16120f]/45">Click any empty date on the calendar to open the schedule form with that date pre-filled.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Timeline view --}}
    <div x-show="view==='timeline'" x-cloak>
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-3">
                @if ($timeline->isEmpty())
                <div class="rounded-3xl bg-white p-10 text-center shadow-sm ring-1 ring-black/5">
                    <svg class="mx-auto h-10 w-10 text-[#16120f]/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
                    <p class="mt-3 text-sm text-[#16120f]/45">Nothing scheduled for {{ $start->format('F Y') }}.</p>
                    @if ($canAdd)
                    <button type="button" @click="showForm=true; view='calendar'" class="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-[#16120f] px-4 py-2 text-xs font-medium text-white">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                        Add something
                    </button>
                    @endif
                </div>
                @else
                @php $prevDate = null; @endphp
                @foreach ($timeline as $item)
                @php
                    $dateLabel = $item['sort']->format('l, d F');
                    $badgeMap  = [
                        'Project'      => ['bg-[#e8ddd4] text-[#7a5c38]', 'Project'],
                        'Consultation' => ['bg-[#dde8e4] text-[#2e5e52]', 'Consultation'],
                        'Availability' => ['bg-[#dde4e8] text-[#2e4a5e]', 'Slot'],
                        'Schedule'     => ['bg-[#e8e4dd] text-[#5e522e]', 'Schedule'],
                        'Lead'         => ['bg-[#e8dde8] text-[#5e2e5e]', 'Lead'],
                    ];
                    [$badgeCls, $badgeShort] = $badgeMap[$item['badge']] ?? ['bg-[#f6f1ea] text-[#9b7b4b]', $item['badge']];
                @endphp

                @if ($dateLabel !== $prevDate)
                <div class="flex items-center gap-3 pt-2 first:pt-0">
                    <span class="text-xs font-semibold text-[#16120f]/45">{{ $dateLabel }}</span>
                    <span class="flex-1 border-t border-[#16120f]/10"></span>
                </div>
                @php $prevDate = $dateLabel; @endphp
                @endif

                <div class="group flex items-start gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="shrink-0 text-right">
                        <p class="text-xs font-semibold text-[#16120f]">{{ $item['sort']->format('g:i') }}</p>
                        <p class="text-[10px] uppercase text-[#16120f]/40">{{ $item['sort']->format('A') }}</p>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div class="min-w-0">
                                @if ($item['href'])
                                <a href="{{ $item['href'] }}" class="block truncate font-medium text-[#16120f] hover:text-[#9b7b4b] transition">{{ $item['label'] }}</a>
                                @else
                                <p class="truncate font-medium text-[#16120f]">{{ $item['label'] }}</p>
                                @endif
                                <p class="mt-0.5 text-xs text-[#16120f]/50">{{ $item['meta'] }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $badgeCls }}">{{ $badgeShort }}</span>
                                @if ($item['deletable'])
                                <button
                                    type="button"
                                    wire:click="deleteSchedule({{ $item['id'] }})"
                                    wire:confirm="Remove this schedule?"
                                    class="opacity-0 group-hover:opacity-100 rounded-lg p-1 text-[#16120f]/30 transition hover:bg-rose-50 hover:text-rose-600"
                                    aria-label="Delete schedule"
                                >
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>

            <div class="space-y-4">
                @if ($followUps->isNotEmpty())
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#16120f]/45">Lead follow-ups</p>
                    <ul class="mt-3 divide-y divide-[#16120f]/8">
                        @foreach ($followUps as $lead)
                        <li class="py-2.5">
                            <a href="{{ route('app.leads.show', $lead) }}" class="flex items-center justify-between text-sm group">
                                <span class="truncate font-medium group-hover:text-[#9b7b4b] transition">{{ $lead->name }}</span>
                                <span class="ml-2 shrink-0 rounded-full bg-[#f6f1ea] px-2 py-0.5 text-[10px] font-medium text-[#9b7b4b]">{{ $lead->follow_up_at->format('d M') }}</span>
                            </a>
                            <p class="text-[10px] text-[#16120f]/40">{{ $lead->lead_number }}</p>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
