@props(['url', 'title' => 'Video'])

@php $embed = \App\Support\YoutubeEmbed::embedUrl($url); @endphp

@if ($embed)
    <div {{ $attributes->merge(['class' => 'relative aspect-video overflow-hidden rounded-[28px] bg-[#16120f] shadow-sm ring-1 ring-black/5']) }}>
        <iframe
            src="{{ $embed }}?rel=0&modestbranding=1"
            class="absolute inset-0 h-full w-full border-0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
            loading="lazy"
            referrerpolicy="strict-origin-when-cross-origin"
            title="{{ $title }}"
        ></iframe>
    </div>
@endif
