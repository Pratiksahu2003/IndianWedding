<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Clients</h1>
        @if ($canCreate)
        <a href="{{ route('app.clients.create') }}" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">New client</a>
        @endif
    </div>

    <x-swal-flash />

    <input wire:model.live.debounce.300ms="search" class="w-full rounded-2xl bg-white px-4 py-2 text-sm" placeholder="Search clients">

    <div class="divide-y rounded-3xl bg-white">
        @forelse ($clients as $client)
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 text-sm">
            <div>
                <p class="font-medium">{{ $client->name }}</p>
                <p class="text-xs opacity-50">{{ $client->customer_number }} · {{ $client->email ?: 'No email' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs opacity-50">{{ $client->projects_count }} projects</span>
                @if ($canEdit)
                <a href="{{ route('app.clients.edit', $client) }}" class="text-xs text-[#9b7b4b]">Edit</a>
                @endif
                @if ($canDelete)
                <button type="button" wire:click="delete({{ $client->id }})" wire:confirm="Remove this client?" class="text-xs text-rose-700">Delete</button>
                @endif
            </div>
        </div>
        @empty
        <div class="px-5 py-12 text-center">
            <p>No clients yet.</p>
            @if ($canCreate)
            <a href="{{ route('app.clients.create') }}" class="mt-4 inline-block rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create client</a>
            @endif
        </div>
        @endforelse
    </div>

    <div>{{ $clients->links() }}</div>
</div>
