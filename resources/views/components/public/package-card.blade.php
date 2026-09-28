@props([
    'package',
    'showPrice' => true,
    'featureLimit' => 5,
    'compact' => false,
])

<a href="{{ $package->publicUrl() }}" {{ $attributes->merge(['class' => 'group flex h-full flex-col overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-black/5 transition hover:-translate-y-1 hover:shadow-xl']) }}>
    <div class="relative aspect-[4/3] overflow-hidden bg-[#f6f1ea]">
        <img
            src="{{ $package->coverUrl() }}"
            alt="{{ $package->name }}"
            class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
            loading="lazy"
        >
        @if ($package->hasYoutubeVideo())
            <span class="absolute bottom-3 right-3 rounded-full bg-[#16120f]/80 px-3 py-1 text-xs text-white">▶ Video</span>
        @endif
        @if ($package->package_type === 'wedding' && $package->price > 0)
            <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-sm font-medium text-[#16120f]">{{ \App\Support\Money::format($package->price) }}</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-6">
        <p class="text-xs uppercase tracking-[0.16em] text-[#9b7b4b]">{{ $package->typeLabel() }}</p>
        <h3 class="mt-2 font-[Cormorant_Garamond] text-2xl leading-tight group-hover:text-[#9b7b4b]">{{ $package->name }}</h3>
        @if ($package->description && ! $compact)
            <p class="mt-2 line-clamp-2 text-sm text-[#16120f]/65">{{ strip_tags($package->description) }}</p>
        @endif
        @if ($package->items->isNotEmpty() && ! $compact)
            <ul class="mt-4 space-y-1.5 text-sm text-[#16120f]/75">
                @foreach ($package->items->take($featureLimit) as $item)
                    <li class="flex gap-2"><span class="text-[#9b7b4b]">•</span><span>{{ $item->name }}</span></li>
                @endforeach
                @if ($package->items->count() > $featureLimit)
                    <li class="text-[#9b7b4b]">+ {{ $package->items->count() - $featureLimit }} more</li>
                @endif
            </ul>
        @endif
        <p class="mt-auto pt-5 text-sm text-[#9b7b4b]">View details →</p>
    </div>
</a>
