@php
    $days = ($project && $project->wedding_date)
        ? now()->startOfDay()->diffInDays($project->wedding_date, false)
        : null;
    $quickLinks = [
        ['client.project', 'Project', $project ?? null],
        ['client.timeline', 'Timeline', true],
        ['client.payments', 'Payments', true],
        ['client.gallery', 'Gallery', true],
    ];
@endphp

<div class="mx-auto max-w-5xl space-y-10">
    <x-swal-flash />

    <section class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-white/[0.08] to-white/[0.02] px-6 py-8 ring-1 ring-white/10 md:px-8">
        <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-[#c4a574]/10 blur-3xl"></div>
        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#c4a574]">Your wedding portal</p>
        <h1 class="mt-3 font-[Cormorant_Garamond] text-4xl md:text-5xl">{{ $customer->name }}</h1>

        @if ($project)
            <p class="mt-4 text-white/65">
                <a href="{{ route('client.project', $project) }}" class="font-medium text-white underline decoration-white/25 underline-offset-4 hover:text-[#e2c48a]">{{ $project->title }}</a>
                @if ($project->wedding_date)
                    · {{ $project->wedding_date->toFormattedDateString() }}
                @endif
            </p>

            @if (! is_null($days))
                <div class="mt-8 inline-flex items-end gap-3">
                    <p class="font-[Cormorant_Garamond] text-6xl leading-none text-[#c4a574] md:text-7xl">{{ max(0, $days) }}</p>
                    <p class="pb-2 text-lg text-white/50">days to go</p>
                </div>
            @endif
        @else
            <p class="mt-6 max-w-md text-white/55">Your wedding workspace will appear here once your booking is confirmed. We cannot wait to capture your story.</p>
        @endif
    </section>

    @if ($project)
        <section>
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-white/35">Quick access</p>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ($quickLinks as [$route, $label, $enabled])
                    @if ($enabled === true || $enabled)
                        <a href="{{ route($route, $enabled instanceof \App\Models\Project ? $enabled : []) }}" class="rounded-2xl bg-white/[0.06] px-4 py-5 text-center ring-1 ring-white/10 transition hover:bg-white/10 hover:ring-[#c4a574]/30">
                            <span class="text-sm font-medium text-white/85">{{ $label }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </section>

        @if ($project->paymentMilestones->isNotEmpty())
            <section>
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-white/35">Payment progress</p>
                <div class="grid gap-3 md:grid-cols-3">
                    @foreach ($project->paymentMilestones as $m)
                        @php $paid = $m->status === \App\Enums\PaymentStatus::Paid; @endphp
                        <div class="rounded-2xl p-5 ring-1 {{ $paid ? 'bg-emerald-500/10 ring-emerald-500/20' : 'bg-white/[0.05] ring-white/10' }}">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/40">{{ $m->name }}</p>
                            <p class="mt-2 text-3xl font-semibold">{{ $m->percentage }}%</p>
                            <p class="mt-1 text-sm capitalize {{ $paid ? 'text-emerald-400/80' : 'text-white/45' }}">{{ str_replace('_', ' ', $m->status->value) }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</div>
