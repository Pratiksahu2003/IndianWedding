<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Clients</h1>
<input wire:model.live.debounce.300ms="search" class="mt-6 rounded-2xl bg-white px-4 py-2 text-sm" placeholder="Search">
<div class="mt-6 divide-y rounded-3xl bg-white">
@forelse ($clients as $client)
<div class="flex items-center justify-between px-5 py-4 text-sm"><div><p class="font-medium">{{ $client->name }}</p><p class="text-xs opacity-50">{{ $client->customer_number }} · {{ $client->email }}</p></div><span>{{ $client->projects_count }} projects</span></div>
@empty
<div class="px-5 py-12 text-center">No clients yet.</div>
@endforelse
</div>
<div class="mt-4">{{ $clients->links() }}</div>
</div>
