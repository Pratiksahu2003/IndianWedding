<div class="mx-auto max-w-3xl">
<h1 class="font-[Cormorant_Garamond] text-4xl">Timeline</h1>
<ol class="mt-10 space-y-4">
@foreach ($project?->milestones ?? [] as $m)
<li class="rounded-3xl bg-white/5 p-5 {{ $m->completed_at ? 'text-[#c4a574]' : 'text-white/50' }}">{{ $m->label }}</li>
@endforeach
</ol>
</div>
