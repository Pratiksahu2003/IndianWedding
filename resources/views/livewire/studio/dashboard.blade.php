@php
    $hour = now('Asia/Kolkata')->hour;
    $hello = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $name = auth()->user()->name;
@endphp
<div class="space-y-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm text-[#16120f]/50">{{ now('Asia/Kolkata')->toFormattedDateString() }}</p>
            <h1 class="mt-1 font-[Cormorant_Garamond] text-4xl md:text-5xl">{{ $hello }}, {{ Str::before($name, ' ') }}</h1>
            <p class="mt-2 max-w-xl text-sm text-[#16120f]/60">Your Unik Studio desk — leads, weddings, money and delivery in one place.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('app.leads.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#16120f] px-4 py-2.5 text-sm font-medium text-white">
                <x-studio.icon name="plus" class="h-4 w-4" /> New lead
            </a>
            <a href="{{ route('app.consultations.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm shadow-sm">Consultations</a>
            <a href="{{ route('app.website') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm shadow-sm">Website</a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['New leads', $stats['new_leads'], 'Waiting in the pipeline', route('app.leads.index'), 'leads'],
            ['Active projects', $stats['active_projects'], $stats['upcoming_weddings'].' upcoming weddings', route('app.projects.index'), 'projects'],
            ['This month', \App\Support\Money::format($stats['monthly_revenue']), 'Collected in '.now()->format('M Y'), route('app.payments.index'), 'payments'],
            ['Overdue', $stats['overdue_payments'], \App\Support\Money::format($stats['pending_payments']).' still due', route('app.payments.index'), 'invoices'],
        ] as [$label, $value, $hint, $href, $icon])
            <a href="{{ $href }}" class="group rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/4 transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#16120f]/40">{{ $label }}</p>
                    <span class="rounded-xl bg-[#f6f1ea] p-2 text-[#9b7b4b] group-hover:bg-[#16120f] group-hover:text-[#e2c48a]">
                        <x-studio.icon :name="$icon" class="h-4 w-4" />
                    </span>
                </div>
                <p class="mt-4 text-4xl font-semibold tracking-tight">{{ $value }}</p>
                <p class="mt-2 text-sm text-[#16120f]/50">{{ $hint }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl bg-[#16120f] p-5 text-[#f6f1ea]">
            <p class="text-xs uppercase tracking-[0.16em] text-white/40">All-time collected</p>
            <p class="mt-3 text-4xl font-semibold tracking-tight text-[#e2c48a]">{{ \App\Support\Money::format($stats['revenue']) }}</p>
            <p class="mt-2 text-sm text-white/50">{{ $stats['conversion'] }}% lead-to-booking conversion · {{ $stats['total_leads'] }} leads</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/4">
            <p class="text-xs uppercase tracking-[0.16em] text-[#16120f]/40">Open tasks</p>
            <p class="mt-3 text-4xl font-semibold tracking-tight">{{ $stats['open_tasks'] }}</p>
            <a href="{{ route('app.tasks.index') }}" class="mt-2 inline-block text-sm text-[#9b7b4b]">Go to tasks →</a>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/4">
            <p class="text-xs uppercase tracking-[0.16em] text-[#16120f]/40">Pipeline health</p>
            <p class="mt-3 text-4xl font-semibold tracking-tight">{{ $stats['total_leads'] }}</p>
            <a href="{{ route('app.leads.pipeline') }}" class="mt-2 inline-block text-sm text-[#9b7b4b]">Open pipeline →</a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/4 xl:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="font-[Cormorant_Garamond] text-2xl">Projects by stage</h2>
                <a href="{{ route('app.projects.index') }}" class="text-sm text-[#9b7b4b]">All projects</a>
            </div>
            <canvas id="projectsChart" class="mt-6" height="120"></canvas>
        </section>
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/4">
            <div class="flex items-center justify-between">
                <h2 class="font-[Cormorant_Garamond] text-2xl">Upcoming weddings</h2>
                <a href="{{ route('app.calendar') }}" class="text-sm text-[#9b7b4b]">Calendar</a>
            </div>
            <ul class="mt-4 divide-y divide-[#16120f]/8">
                @forelse ($stats['upcoming_list'] as $project)
                    <li class="py-3">
                        <a href="{{ route('app.projects.show', $project) }}" class="flex items-start justify-between gap-3">
                            <span>
                                <span class="block font-medium">{{ $project->title }}</span>
                                <span class="text-xs text-[#16120f]/45">{{ $project->customer?->name }} · {{ $project->city ?: $project->venue }}</span>
                            </span>
                            <span class="shrink-0 rounded-full bg-[#f6f1ea] px-2 py-1 text-xs">{{ optional($project->wedding_date)?->format('d M') }}</span>
                        </a>
                    </li>
                @empty
                    <li class="py-8 text-sm text-[#16120f]/45">No upcoming wedding dates yet.</li>
                @endforelse
            </ul>
        </section>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/4">
            <div class="flex items-center justify-between">
                <h2 class="font-[Cormorant_Garamond] text-2xl">New enquiries</h2>
                <a href="{{ route('app.leads.index') }}" class="text-sm text-[#9b7b4b]">Leads</a>
            </div>
            <ul class="mt-4 space-y-3">
                @forelse ($stats['new_leads_list'] as $lead)
                    <li>
                        <a href="{{ route('app.leads.show', $lead) }}" class="flex items-center justify-between text-sm">
                            <span>{{ $lead->name }}</span>
                            <span class="text-xs text-[#16120f]/40">{{ $lead->created_at->diffForHumans() }}</span>
                        </a>
                    </li>
                @empty
                    <li class="text-sm text-[#16120f]/45">No new leads. Share the enquiry form.</li>
                @endforelse
            </ul>
        </section>
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/4">
            <div class="flex items-center justify-between">
                <h2 class="font-[Cormorant_Garamond] text-2xl">Needs attention</h2>
                <a href="{{ route('app.payments.index') }}" class="text-sm text-[#9b7b4b]">Payments</a>
            </div>
            <ul class="mt-4 space-y-3 text-sm">
                @forelse ($stats['overdue_list'] as $item)
                    <li class="flex items-center justify-between">
                        <span>{{ $item->project?->title ?? $item->name ?? 'Payment' }}</span>
                        <span class="text-rose-700">{{ \App\Support\Money::format($item->amount) }}</span>
                    </li>
                @empty
                    <li class="text-[#16120f]/45">Nothing overdue. Nice work.</li>
                @endforelse
            </ul>
        </section>
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/4">
            <h2 class="font-[Cormorant_Garamond] text-2xl">Recent activity</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @forelse ($stats['recent_activity'] as $item)
                    <li class="flex justify-between gap-3">
                        <span class="min-w-0 truncate">{{ $item->action }}</span>
                        <span class="shrink-0 text-xs text-[#16120f]/40">{{ $item->created_at->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="text-[#16120f]/45">Activity will appear as the studio works.</li>
                @endforelse
            </ul>
        </section>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('projectsChart');
        if (!el || !window.Chart) return;
        const labels = {!! json_encode(array_map(fn ($s) => str_replace('_', ' ', $s), array_keys($stats['projects_by_status']->toArray()))) !!};
        const data = {!! json_encode(array_values($stats['projects_by_status']->toArray())) !!};
        new Chart(el, {
            type: 'bar',
            data: {
                labels,
                datasets: [{ data, backgroundColor: '#c4a574', borderRadius: 8, maxBarThickness: 28 }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#6b645c', maxRotation: 45 } },
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(22,18,15,0.06)' } }
                }
            }
        });
    });
</script>
