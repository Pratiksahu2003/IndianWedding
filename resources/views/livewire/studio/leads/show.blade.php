<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-3xl bg-white p-8">
        <p class="text-xs uppercase tracking-[0.2em] opacity-50">{{ $lead->lead_number }}</p>
        <h1 class="font-[Cormorant_Garamond] text-4xl">{{ $lead->name }}</h1>
        <p class="mt-2 text-sm opacity-70">{{ $lead->email }} · {{ $lead->phone }} · {{ $lead->city }}</p>
        <form wire:submit="updateStatus" class="mt-6 flex flex-wrap gap-3">
            <select wire:model="status" class="rounded-2xl bg-[#f6f1ea] px-3 py-2 text-sm">
                @foreach ($statuses as $status)<option value="{{ $status->value }}">{{ $status->label() }}</option>@endforeach
            </select>
            <button class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Update</button>
        </form>
        @if ($lead->status !== \App\Enums\LeadStatus::Booked)
            <button wire:click="convert" wire:confirm="Create a project from this lead?" class="mt-4 rounded-full border border-[#16120f] px-4 py-2 text-sm">Convert to booking</button>
        @endif
        <form wire:submit="addNote" class="mt-8">
            <x-studio.rich-textarea model="note" placeholder="Add a note" :rows="4" class="w-full" />
            <button class="mt-3 rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Save note</button>
        </form>
        <ul class="mt-6 space-y-3 text-sm">
            @foreach ($lead->notes as $note)
                <li class="cms-rich rounded-2xl bg-[#f6f1ea] p-3">{!! rich_html($note->body) !!} <span class="opacity-50">· {{ $note->user?->name }}</span></li>
            @endforeach
        </ul>
    </div>
    <aside class="rounded-3xl bg-white p-6 text-sm">
        <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Activity</h2>
        <ul class="mt-4 space-y-3">
            @foreach ($lead->activities as $activity)
                <li>{{ $activity->body }} <span class="opacity-40">{{ $activity->created_at->diffForHumans() }}</span></li>
            @endforeach
        </ul>
        <form wire:submit="scheduleFollowup" class="mt-6 space-y-2">
            <input type="datetime-local" wire:model="follow_up_at" class="w-full rounded-2xl bg-[#f6f1ea] px-3 py-2">
            <button class="rounded-full bg-[#16120f] px-4 py-2 text-white">Schedule follow-up</button>
        </form>
    </aside>
</div>
