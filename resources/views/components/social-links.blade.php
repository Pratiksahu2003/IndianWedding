@props([
    'class' => '',
    'iconClass' => 'h-5 w-5',
    'theme' => 'dark',
])

@php
    $links = social_links();
    $buttonClass = $theme === 'light'
        ? 'inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#16120f]/10 bg-[#f6f1ea] text-[#16120f]/70 transition hover:border-[#c4a574]/60 hover:bg-white hover:text-[#9b7b4b]'
        : 'inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/5 text-white/80 transition hover:border-[#c4a574]/50 hover:bg-white/10 hover:text-[#e2c48a]';
@endphp

@if (count($links) > 0)
    <div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2 '.$class]) }} role="list">
        @foreach ($links as $link)
            <a
                href="{{ $link['url'] }}"
                target="_blank"
                rel="noopener noreferrer"
                class="{{ $buttonClass }}"
                aria-label="{{ $link['label'] }}"
                title="{{ $link['label'] }}"
                role="listitem"
            >
                @include('components.partials.social-icon', ['platform' => $link['id'], 'class' => $iconClass])
            </a>
        @endforeach
    </div>
@endif
