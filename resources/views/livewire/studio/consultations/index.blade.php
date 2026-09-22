<div class="space-y-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Consultations</h1>
            <p class="mt-1 text-sm text-[#16120f]/50">Open booking slots for clients and track scheduled consultations.</p>
        </div>
        @if ($canCreate)
        <a href="{{ route('app.consultations.create') }}" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Open a slot</a>
        @endif
    </div>

    <x-swal-flash />

    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-[#9b7b4b]">Open slots</h2>
            <span class="text-xs text-[#16120f]/45">{{ $upcomingSlots->count() }} upcoming</span>
        </div>

        <div class="grid gap-3 md:grid-cols-2">
            @forelse ($upcomingSlots as $slot)
            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        @if ($slot->title)
                        <p class="font-medium text-[#16120f]">{{ $slot->title }}</p>
                        @endif
                        <p class="{{ $slot->title ? 'mt-1 text-sm text-[#16120f]/70' : 'font-medium text-[#16120f]' }}">
                            {{ $slot->formattedDate() }}
                        </p>
                        <p class="mt-0.5 text-sm text-[#16120f]/60">
                            {{ $slot->formattedStart() }} – {{ $slot->formattedEnd() }}
                            @if ($slot->staff)
                            · {{ $slot->staff->name }}
                            @endif
                        </p>
                        @if ($slot->description)
                        <p class="mt-2 text-sm leading-relaxed text-[#16120f]/55">{{ $slot->description }}</p>
                        @endif
                        <p class="mt-2">
                            @if ($slot->is_available)
                            <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-800">Available</span>
                            @else
                            <span class="rounded-full bg-[#f6f1ea] px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-[#9b7b4b]">Unavailable</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-2">
                        @if ($canEdit)
                        <a href="{{ route('app.consultations.edit', $slot) }}" class="text-xs text-[#9b7b4b]">Edit</a>
                        <button type="button" wire:click="toggleAvailability({{ $slot->id }})" class="text-xs text-[#16120f]/50">
                            {{ $slot->is_available ? 'Close' : 'Reopen' }}
                        </button>
                        @endif
                        @if ($canDelete)
                        <button type="button" wire:click="deleteSlot({{ $slot->id }})" wire:confirm="Remove this slot?" class="text-xs text-rose-700">Delete</button>
                        @endif
                    </div>
                </div>
            </article>
            @empty
            <div class="rounded-3xl bg-white p-10 text-center md:col-span-2">
                <p class="text-[#16120f]/60">No upcoming slots yet.</p>
                @if ($canCreate)
                <a href="{{ route('app.consultations.create') }}" class="mt-4 inline-block rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Open your first slot</a>
                @endif
            </div>
            @endforelse
        </div>

        @if ($pastSlots->isNotEmpty())
        <details class="rounded-3xl bg-white p-5 ring-1 ring-black/5">
            <summary class="cursor-pointer text-sm font-medium text-[#16120f]/70">Past slots ({{ $pastSlots->count() }})</summary>
            <ul class="mt-4 space-y-3">
                @foreach ($pastSlots as $slot)
                <li class="flex flex-wrap items-center justify-between gap-2 border-t border-[#16120f]/5 pt-3 text-sm">
                    <span class="text-[#16120f]/60">
                        @if ($slot->title){{ $slot->title }} · @endif
                        {{ $slot->formattedDate() }} · {{ $slot->formattedStart() }}–{{ $slot->formattedEnd() }}
                    </span>
                    @if ($canDelete)
                    <button type="button" wire:click="deleteSlot({{ $slot->id }})" wire:confirm="Remove this slot?" class="text-xs text-rose-700">Delete</button>
                    @endif
                </li>
                @endforeach
            </ul>
        </details>
        @endif
    </section>

    <section class="space-y-4">
        <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-[#9b7b4b]">Booked consultations</h2>
        <div class="divide-y overflow-hidden rounded-3xl bg-white ring-1 ring-black/5">
            @forelse ($consultations as $c)
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 text-sm">
                <div>
                    <p class="font-medium">{{ $c->name }}</p>
                    <p class="text-xs text-[#16120f]/50">
                        {{ optional($c->starts_at)->timezone(config('app.timezone'))->format('D, M j · g:i A') }}
                        · {{ str_replace('_', ' ', $c->status) }}
                        @if ($c->email) · {{ $c->email }} @endif
                    </p>
                </div>
                @if ($canDelete)
                <button type="button" wire:click="deleteConsultation({{ $c->id }})" wire:confirm="Remove this consultation?" class="text-xs text-rose-700">Delete</button>
                @endif
            </div>
            @empty
            <div class="px-5 py-10 text-center text-sm text-[#16120f]/50">No consultations booked yet.</div>
            @endforelse
        </div>
    </section>
</div>
