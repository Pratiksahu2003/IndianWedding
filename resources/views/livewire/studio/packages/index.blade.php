<div class="grid gap-8 lg:grid-cols-2">
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="font-[Cormorant_Garamond] text-4xl">Packages</h1>
            @if ($canCreate)
            <button type="button" wire:click="create" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">New package</button>
            @endif
        </div>

        @if (session('status'))
            <div class="rounded-2xl bg-[#16120f] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
        @endif

        <div class="space-y-4">
            @forelse ($packages as $package)
            <article class="rounded-3xl bg-white p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl"><a href="{{ route('app.packages.show', $package) }}">{{ $package->name }}</a></h2>
                        <p class="text-sm opacity-70">{{ \App\Support\Money::format($package->price) }}</p>
                        @if ($package->is_public)
                        <a class="mt-2 inline-block text-xs text-[#9b7b4b]" href="{{ $package->publicUrl() }}">Public page</a>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        @if ($canEdit)
                        <button type="button" wire:click="edit({{ $package->id }})" class="text-xs text-[#9b7b4b]">Edit</button>
                        @endif
                        @if ($canDelete)
                        <button type="button" wire:click="delete({{ $package->id }})" wire:confirm="Remove this package?" class="text-xs text-rose-700">Delete</button>
                        @endif
                    </div>
                </div>
            </article>
            @empty
            <p>No packages yet.</p>
            @endforelse
        </div>
    </div>

    @if ($canCreate || $editingId)
    <form wire:submit="save" class="rounded-3xl bg-white p-6 h-fit">
        <h2 class="text-sm uppercase tracking-[0.2em] opacity-50">{{ $editingId ? 'Edit package' : 'New package' }}</h2>
        <div class="mt-4 grid gap-3">
            <input wire:model="name" placeholder="Name" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <input type="number" wire:model="price" placeholder="Price in rupees" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <x-studio.rich-textarea model="description" :rows="5" />
            <div class="flex gap-2">
                <button class="rounded-full bg-[#16120f] px-4 py-3 text-white">{{ $editingId ? 'Save changes' : 'Create' }}</button>
                @if ($editingId)
                <button type="button" wire:click="cancel" class="rounded-full bg-white px-4 py-3 text-sm ring-1 ring-black/10">Cancel</button>
                @endif
            </div>
        </div>
    </form>
    @endif
</div>
