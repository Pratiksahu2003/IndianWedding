<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Galleries</h1>
<div class="mt-6 grid gap-4 md:grid-cols-2">
@forelse ($galleries as $gallery)
<article class="rounded-3xl bg-white p-6"><p>{{ $gallery->title }}</p><p class="text-sm opacity-60">{{ $gallery->is_released ? 'Released' : 'Internal' }} · {{ $gallery->project?->title }}</p></article>
@empty
<p class="rounded-3xl bg-white p-10">No galleries yet.</p>
@endforelse
</div>
</div>
