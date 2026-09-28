<div class="mx-auto max-w-6xl pt-8">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#c4a574]">Delivery</p>
    <h1 class="mt-2 font-[Cormorant_Garamond] text-4xl">Gallery</h1>
    <p class="mt-2 text-sm text-white/55">Preview and favourite photos from your wedding.</p>
@if (! $gallery)
    <p class="mt-10 rounded-2xl border border-dashed border-white/15 px-6 py-10 text-center text-sm text-white/50">Your gallery preview is being prepared. We will notify you when it is ready.</p>
@else
    <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-4">
        @foreach ($gallery->items as $item)
            <figure class="relative overflow-hidden rounded-3xl">
                <img src="{{ route('files.show', ['file' => $item->file, 'preview' => 1]) }}" class="h-48 w-full object-cover" alt="">
                <button wire:click="toggleFavorite({{ $item->id }})" class="absolute right-3 top-3 text-white">{{ $item->is_favorite ? '♥' : '♡' }}</button>
            </figure>
        @endforeach
    </div>
    @if ($gallery->allow_download)
        <p class="mt-6 text-sm text-white/60">Full-resolution downloads are enabled after final payment.</p>
    @endif
@endif
</div>
