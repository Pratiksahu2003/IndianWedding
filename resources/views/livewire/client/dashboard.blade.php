<div class="mx-auto max-w-5xl">
<p class="text-sm uppercase tracking-[0.3em] text-[#c4a574]">Welcome</p>
<h1 class="mt-3 font-[Cormorant_Garamond] text-5xl">{{ $customer->name }}</h1>
@if ($project)
    @php $days = $project->wedding_date ? now()->startOfDay()->diffInDays($project->wedding_date, false) : null; @endphp
    @if ($project)
    <p class="mt-4 text-white/70"><a href="{{ route('client.project', $project) }}" class="underline decoration-white/30">{{ $project->title }}</a> · {{ optional($project->wedding_date)?->toFormattedDateString() }}</p>
    @if (!is_null($days))
        <p class="mt-8 font-[Cormorant_Garamond] text-7xl text-[#c4a574]">{{ max(0, $days) }} <span class="text-3xl">days</span></p>
    @endif
    <div class="mt-10 grid gap-4 md:grid-cols-3">
        @foreach ($project->paymentMilestones as $m)
            <div class="rounded-3xl bg-white/5 p-5">
                <p class="text-xs uppercase tracking-[0.2em] text-white/40">{{ $m->name }}</p>
                <p class="mt-2 text-2xl">{{ $m->percentage }}%</p>
                <p class="text-sm text-white/50">{{ $m->status->value }}</p>
            </div>
        @endforeach
    </div>
@else
    <p class="mt-8 text-white/60">Your wedding workspace will appear after booking.</p>
@endif
</div>
