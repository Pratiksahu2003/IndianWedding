<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Packages & Services</h1>
        @if ($canCreate)
            <a href="{{ route('app.packages.create') }}" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">New package</a>
        @endif
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach (['all' => 'All', 'service' => 'Services', 'wedding' => 'Wedding', 'production' => 'Production', 'addon' => 'Add-ons'] as $key => $label)
            <button type="button" wire:click="$set('filter', '{{ $key }}')" class="rounded-full px-4 py-2 text-sm transition {{ $filter === $key ? 'bg-[#16120f] text-white' : 'bg-white text-[#16120f]/70 ring-1 ring-black/10' }}">{{ $label }}</button>
        @endforeach
    </div>

    <x-swal-flash />

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($packages as $package)
            <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.14em] text-[#9b7b4b]">{{ $package->typeLabel() }}</p>
                        <h2 class="text-xl"><a href="{{ route('app.packages.show', $package) }}">{{ $package->name }}</a></h2>
                        @if ($package->price > 0)
                            <p class="mt-1 text-sm opacity-70">{{ \App\Support\Money::format($package->price) }}</p>
                        @else
                            <p class="mt-1 text-sm opacity-70">Custom pricing</p>
                        @endif
                        <p class="mt-1 text-xs opacity-50">{{ $package->items->count() }} features</p>
                        @if ($package->is_public)
                            <a class="mt-2 inline-block text-xs text-[#9b7b4b]" href="{{ $package->publicUrl() }}" target="_blank">Public page</a>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        @if ($canEdit)
                            <a href="{{ route('app.packages.edit', $package) }}" class="text-xs text-[#9b7b4b]">Edit</a>
                        @endif
                        @if ($canDelete)
                            <button type="button" wire:click="delete({{ $package->id }})" wire:confirm="Remove this package?" class="text-xs text-rose-700">Delete</button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl bg-white p-10 text-center md:col-span-2">
                <p>No packages in this category yet.</p>
                @if ($canCreate)
                    <a href="{{ route('app.packages.create') }}" class="mt-4 inline-block rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create package</a>
                @endif
            </div>
        @endforelse
    </div>
</div>
