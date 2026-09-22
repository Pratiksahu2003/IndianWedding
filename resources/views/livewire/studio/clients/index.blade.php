<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Clients</h1>
        @if ($canCreate)
        <button type="button" wire:click="create" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">New client</button>
        @endif
    </div>

    @if (session('status'))
        <div class="rounded-2xl bg-[#16120f] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
    @endif

    @if ($showForm && ($canCreate || $editingId))
    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="font-[Cormorant_Garamond] text-2xl">{{ $editingId ? 'Edit client' : 'New client' }}</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <input wire:model="name" placeholder="Name *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="email" type="email" placeholder="Email" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="phone" placeholder="Phone" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="city" placeholder="City" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="wedding_date" type="date" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <textarea wire:model="notes" placeholder="Notes" rows="2" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2 lg:col-span-3"></textarea>
        </div>
        @error('name') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">{{ $editingId ? 'Save changes' : 'Create client' }}</button>
            <button type="button" wire:click="cancel" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Cancel</button>
        </div>
    </form>
    @endif

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
                <button type="button" wire:click="edit({{ $client->id }})" class="text-xs text-[#9b7b4b]">Edit</button>
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
            <button type="button" wire:click="create" class="mt-4 rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create client</button>
            @endif
        </div>
        @endforelse
    </div>

    <div>{{ $clients->links() }}</div>
</div>
