<div class="mx-auto max-w-3xl pt-8">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#c4a574]">Planning</p>
    <h1 class="mt-2 font-[Cormorant_Garamond] text-4xl">Timeline</h1>
    <p class="mt-2 text-sm text-white/55">Key milestones on your wedding journey with Unik Studio.</p>

    @if (($project?->milestones ?? collect())->isEmpty())
        <p class="mt-10 rounded-2xl border border-dashed border-white/15 px-6 py-10 text-center text-sm text-white/50">
            Your timeline will be shared here as planning progresses.
        </p>
    @else
        <ol class="mt-10 space-y-4">
            @foreach ($project->milestones as $m)
                <li class="rounded-3xl p-5 ring-1 {{ $m->completed_at ? 'bg-[#c4a574]/10 text-[#c4a574] ring-[#c4a574]/20' : 'bg-white/5 text-white/70 ring-white/10' }}">
                    {{ $m->label }}
                    @if ($m->completed_at)
                        <span class="mt-1 block text-xs text-[#c4a574]/70">Completed {{ $m->completed_at->format('j M Y') }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</div>
