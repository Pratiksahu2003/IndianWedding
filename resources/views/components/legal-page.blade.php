@props([
    'title',
    'description' => null,
    'current',
    'updated' => '22 September 2026',
])

@php
    $brand = site('brand.name', 'Unik Studio');
    $legalNav = [
        ['key' => 'terms', 'href' => '/terms-and-conditions', 'label' => 'Terms & Conditions'],
        ['key' => 'privacy', 'href' => '/privacy-policy', 'label' => 'Privacy Policy'],
        ['key' => 'cookies', 'href' => '/cookie-policy', 'label' => 'Cookie Policy'],
    ];
@endphp

<section class="relative overflow-hidden bg-[#0c0b0a] text-white">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(196,165,116,0.12),transparent_55%)]"></div>
    <div class="relative mx-auto max-w-6xl px-4 py-14 sm:px-6 sm:py-16 md:py-20">
        <p class="text-xs font-medium uppercase tracking-[0.28em] text-[#c4a574]">Legal</p>
        <h1 class="mt-3 max-w-3xl font-[Cormorant_Garamond] text-4xl leading-tight sm:text-5xl md:text-6xl">{{ $title }}</h1>
        @if ($description)
            <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/65 sm:text-base">{{ $description }}</p>
        @endif
        <p class="mt-6 text-xs uppercase tracking-[0.2em] text-white/40">Last updated · {{ $updated }}</p>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 pb-24 pt-12 sm:px-6 md:pt-16">
    <div class="lg:grid lg:grid-cols-12 lg:gap-10">
        <aside class="mb-10 lg:col-span-4 lg:mb-0">
            <div class="lg:sticky lg:top-24 space-y-6">
                <nav class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-[#16120f]/5" aria-label="Legal documents">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#9b7b4b]">Documents</p>
                    <ul class="mt-4 space-y-1">
                        @foreach ($legalNav as $item)
                            <li>
                                <a
                                    href="{{ $item['href'] }}"
                                    class="block rounded-xl px-3 py-2.5 text-sm transition {{ $current === $item['key'] ? 'bg-[#16120f] font-medium text-white' : 'text-[#16120f]/75 hover:bg-[#f6f1ea] hover:text-[#16120f]' }}"
                                >
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
                <div class="rounded-[28px] bg-[#16120f] p-6 text-sm text-white/80">
                    <p class="font-medium text-white">Questions about these policies?</p>
                    <p class="mt-2 leading-relaxed">Contact {{ $brand }} and we will respond within one business day.</p>
                    <a href="/contact" class="mt-4 inline-flex rounded-full bg-[#c4a574] px-4 py-2 text-sm font-medium text-[#16120f] transition hover:bg-[#d4b888]">Contact us</a>
                </div>
            </div>
        </aside>
        <article class="legal-prose lg:col-span-8">
            <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-[#16120f]/5 sm:p-8 md:p-10">
                {{ $slot }}
            </div>
        </article>
    </div>
</section>
