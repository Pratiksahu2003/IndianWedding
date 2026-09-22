<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Lumina Atelier' }} · Wedding Photography</title>
    <meta name="description" content="{{ $description ?? 'Editorial wedding photography for modern celebrations.' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Lumina Atelier' }}">
    <meta property="og:description" content="{{ $description ?? 'Editorial wedding photography for modern celebrations.' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => $organization->name ?? 'Lumina Atelier',
            'url' => url('/'),
            'areaServed' => 'India',
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600,700|outfit:300,400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#f6f1ea] font-[Outfit] text-[#16120f]">
    <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 bg-white px-3 py-2">Skip to content</a>
    <header x-data="{ open: false }" class="fixed inset-x-0 top-0 z-40 border-b border-[#16120f]/10 bg-[#f6f1ea]/80 backdrop-blur-xl">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="/" class="font-[Cormorant_Garamond] text-2xl tracking-[0.2em]">LUMINA</a>
            <nav class="hidden items-center gap-7 text-sm md:flex">
                @foreach (['about'=>'About','services'=>'Services','packages'=>'Packages','portfolio'=>'Portfolio','gallery'=>'Gallery','faq'=>'FAQ','contact'=>'Contact'] as $href=>$label)
                    <a class="hover:text-[#9b7b4b]" href="/{{ $href }}">{{ $label }}</a>
                @endforeach
                <a href="/book-consultation" class="rounded-full bg-[#16120f] px-4 py-2 text-white">Book consultation</a>
            </nav>
            <button class="md:hidden" @click="open=!open" aria-label="Open menu">Menu</button>
        </div>
        <div x-show="open" x-cloak class="border-t border-[#16120f]/10 bg-[#f6f1ea] px-6 py-4 md:hidden">
            <div class="flex flex-col gap-3 text-sm">
                @foreach (['about','services','packages','portfolio','gallery','testimonials','faq','contact','book-consultation'] as $href)
                    <a href="/{{ $href }}">{{ str_replace('-', ' ', ucfirst($href)) }}</a>
                @endforeach
            </div>
        </div>
    </header>
    <main id="content" class="pt-20">
        {{ $slot ?? '' }}
        @yield('content')
    </main>
    <footer class="mt-24 border-t border-[#16120f]/10 px-6 py-16 text-sm">
        <div class="mx-auto flex max-w-6xl flex-col justify-between gap-6 md:flex-row">
            <div>
                <p class="font-[Cormorant_Garamond] text-3xl">Lumina Atelier</p>
                <p class="mt-2 max-w-sm text-[#16120f]/70">Quiet luxury wedding photography. Films, albums, and archives crafted with restraint.</p>
            </div>
            <div class="flex gap-10">
                <a href="/contact">Contact</a>
                <a href="/login">Client / studio login</a>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script>
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && window.gsap) {
            gsap.registerPlugin(ScrollTrigger);
            gsap.from('[data-reveal]', { y: 24, opacity: 0, duration: .8, stagger: .08, ease: 'power2.out' });
            gsap.utils.toArray('[data-scroll]').forEach((el) => {
                gsap.from(el, { scrollTrigger: { trigger: el, start: 'top 85%' }, y: 30, opacity: 0, duration: .9, ease: 'power2.out' });
            });
        }
    </script>
    @livewireScripts
</body>
</html>
