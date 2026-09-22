<div class="grid gap-8 lg:grid-cols-2">
<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Packages</h1>
<div class="mt-6 space-y-4">
@forelse ($packages as $package)
<article class="rounded-3xl bg-white p-6"><h2 class="text-xl"><a href="{{ route('app.packages.show', $package) }}">{{ $package->name }}</a></h2><p class="text-sm opacity-70">{{ \App\Support\Money::format($package->price) }}</p>@if($package->is_public)<a class="mt-2 inline-block text-xs text-[#9b7b4b]" href="{{ $package->publicUrl() }}">Public page</a>@endif</article>
@empty
<p>No packages yet.</p>
@endforelse
</div>
</div>
<form wire:submit="save" class="rounded-3xl bg-white p-6">
<h2 class="text-sm uppercase tracking-[0.2em] opacity-50">New package</h2>
<div class="mt-4 grid gap-3">
<input wire:model="name" placeholder="Name" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input type="number" wire:model="price" placeholder="Price in rupees" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
<textarea wire:model="description" class="rounded-2xl bg-[#f6f1ea] px-4 py-3"></textarea>
<button class="rounded-full bg-[#16120f] py-3 text-white">Create</button>
</div>
</form>
</div>
