<div class="mx-auto max-w-6xl">
<h1 class="font-[Cormorant_Garamond] text-4xl">Gallery</h1>
@if (! $gallery)
    <p class="mt-8 text-white/60">Your preview is being prepared.</p>
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
