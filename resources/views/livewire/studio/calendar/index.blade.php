<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Calendar</h1>
<input type="month" wire:model.live="month" class="mt-4 rounded-2xl bg-white px-3 py-2">
<div class="mt-6 grid gap-3 md:grid-cols-2">
@foreach ($events as $event)
<div class="rounded-3xl bg-white p-5"><p class="text-xs uppercase opacity-50">{{ $event->type->value }}</p><p class="text-lg">{{ $event->title }}</p><p class="text-sm opacity-60">{{ optional($event->date)?->toFormattedDateString() }} · {{ $event->project?->title }}</p></div>
@endforeach
@foreach ($consults as $c)
<div class="rounded-3xl bg-white p-5"><p class="text-xs uppercase opacity-50">Consultation</p><p class="text-lg">{{ $c->name }}</p><p class="text-sm opacity-60">{{ $c->starts_at }}</p></div>
@endforeach
@if ($events->isEmpty() && $consults->isEmpty())
<p class="rounded-3xl bg-white p-10">Nothing scheduled this month.</p>
@endif
</div>
</div>
