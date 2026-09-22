<div class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl bg-[#16120f] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
    @endif

    <div class="grid gap-8 lg:grid-cols-2">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Consultations</h1>
            <ul class="mt-6 space-y-3">
                @forelse ($consultations as $c)
                <li class="flex items-center justify-between rounded-3xl bg-white p-5 text-sm">
                    <span>{{ $c->name }} · {{ $c->starts_at }} · {{ $c->status }}</span>
                    @if ($canDelete)
                    <button type="button" wire:click="deleteConsultation({{ $c->id }})" wire:confirm="Remove this consultation?" class="text-xs text-rose-700">Delete</button>
                    @endif
                </li>
                @empty
                <li class="rounded-3xl bg-white p-5 text-sm opacity-50">No consultations yet.</li>
                @endforelse
            </ul>

            <h2 class="mt-8 text-sm uppercase tracking-[0.2em] opacity-50">Open slots</h2>
            <ul class="mt-3 space-y-3">
                @forelse ($slots as $slot)
                <li class="flex items-center justify-between rounded-3xl bg-white p-5 text-sm">
                    <span>{{ $slot->date?->toFormattedDateString() ?? $slot->date }} · {{ $slot->start_time }}–{{ $slot->end_time }}</span>
                    @if ($canDelete)
                    <button type="button" wire:click="deleteSlot({{ $slot->id }})" wire:confirm="Remove this slot?" class="text-xs text-rose-700">Delete</button>
                    @endif
                </li>
                @empty
                <li class="rounded-3xl bg-white p-5 text-sm opacity-50">No open slots.</li>
                @endforelse
            </ul>
        </div>

        @if ($canCreate)
        <form wire:submit="addSlot" class="rounded-3xl bg-white p-6 h-fit">
            <h2 class="text-sm uppercase tracking-[0.2em] opacity-50">Open a slot</h2>
            <input type="date" wire:model="date" class="mt-4 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <input type="time" wire:model="start_time" class="mt-3 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <input type="time" wire:model="end_time" class="mt-3 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            @error('start_time') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
            @error('end_time') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
            <button class="mt-4 rounded-full bg-[#16120f] px-4 py-2 text-white">Add slot</button>
        </form>
        @endif
    </div>
</div>
