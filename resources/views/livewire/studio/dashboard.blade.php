<div>
    <h1 class="font-[Cormorant_Garamond] text-4xl">Studio</h1>
    <div class="mt-8 grid gap-4 md:grid-cols-4">
        @foreach ([
            ['Leads', $stats['total_leads']],
            ['New', $stats['new_leads']],
            ['Conversion', $stats['conversion'].'%'],
            ['Active projects', $stats['active_projects']],
            ['Upcoming weddings', $stats['upcoming_weddings']],
            ['Revenue', \App\Support\Money::format($stats['revenue'])],
            ['This month', \App\Support\Money::format($stats['monthly_revenue'])],
            ['Overdue', $stats['overdue_payments']],
        ] as [$label,$value])
            <div class="rounded-3xl bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#16120f]/50">{{ $label }}</p>
                <p class="mt-2 font-[Cormorant_Garamond] text-3xl">{{ $value }}</p>
            </div>
        @endforeach
    </div>
    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl bg-white p-6">
            <h2 class="text-sm uppercase tracking-[0.2em]">Projects by status</h2>
            <canvas id="projectsChart" class="mt-4" height="140"></canvas>
        </div>
        <div class="rounded-3xl bg-white p-6">
            <h2 class="text-sm uppercase tracking-[0.2em]">Recent activity</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @forelse ($stats['recent_activity'] as $item)
                    <li class="flex justify-between"><span>{{ $item->action }}</span><span class="opacity-50">{{ $item->created_at->diffForHumans() }}</span></li>
                @empty
                    <li class="opacity-60">No activity yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('projectsChart');
            if (!el || !window.Chart) return;
            new Chart(el, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($stats['projects_by_status']->toArray())) !!},
                    datasets: [{ data: {!! json_encode(array_values($stats['projects_by_status']->toArray())) !!}, backgroundColor: '#c4a574' }]
                },
                options: { plugins: { legend: { display: false } } }
            });
        });
    </script>
</div>
