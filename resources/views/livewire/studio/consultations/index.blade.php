<div class="grid gap-8 lg:grid-cols-2">
<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Consultations</h1>
<ul class="mt-6 space-y-3">
@foreach ($consultations as $c)
<li class="rounded-3xl bg-white p-5 text-sm">{{ $c->name }} · {{ $c->starts_at }} · {{ $c->status }}</li>
@endforeach
</ul>
</div>
<form wire:submit="addSlot" class="rounded-3xl bg-white p-6">
<h2 class="text-sm uppercase tracking-[0.2em] opacity-50">Open a slot</h2>
<input type="date" wire:model="date" class="mt-4 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input type="time" wire:model="start_time" class="mt-3 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input type="time" wire:model="end_time" class="mt-3 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<button class="mt-4 rounded-full bg-[#16120f] px-4 py-2 text-white">Add slot</button>
</form>
</div>
