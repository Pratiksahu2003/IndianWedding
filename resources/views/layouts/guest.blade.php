<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $title ?? 'Sign in' }} · {{ config('app.name') }}</title>
    @include('partials.brand-head')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:600,700|outfit:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-white font-[Outfit] text-[#0f2744] antialiased">
@php
    $brand = site('brand.name', 'Unik Studio');
    $headline = site('home.headline', 'Capturing Moments, Creating Memories');
@endphp
<div class="grid min-h-screen lg:grid-cols-2">
    <div class="relative isolate h-52 overflow-hidden lg:hidden">
        <img src="{{ \App\Support\UnikStudioAssets::url('auth-couple.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover" width="1600" height="2200" decoding="async">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0b1320] via-[#0b1320]/50 to-transparent"></div>
        <div class="relative z-10 flex h-full flex-col justify-end px-6 pb-5">
            <a href="/" class="inline-flex w-fit">
                <x-brand-logo class="h-12 max-w-[220px] drop-shadow-lg" />
            </a>
            <p class="mt-3 text-2xl font-semibold tracking-tight text-white">{{ $brand }}</p>
            <p class="mt-1 max-w-sm text-sm text-white/80">{{ $headline }}</p>
        </div>
    </div>

    <aside class="relative hidden min-h-screen overflow-hidden lg:block" aria-label="{{ $brand }}">
        <img
            src="{{ \App\Support\UnikStudioAssets::url('auth-couple.jpg') }}"
            alt="Wedding photography by Unik Studio"
            class="absolute inset-0 h-full w-full object-cover"
            width="1600"
            height="2200"
            decoding="async"
            fetchpriority="high"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-[#0b1320] via-[#0b1320]/45 to-[#0b1320]/10"></div>
        <div class="relative z-10 flex h-full min-h-screen flex-col justify-end px-10 pb-12 pt-10 xl:px-14">
            <a href="/" class="inline-flex w-fit">
                <x-brand-logo class="h-14 max-w-[280px] drop-shadow-lg" />
            </a>
            <h2 class="mt-10 max-w-lg font-[Outfit] text-4xl font-extrabold leading-tight tracking-tight text-white xl:text-5xl">{{ $brand }}</h2>
            <p class="mt-3 max-w-md text-base text-white/85">{{ $headline }}</p>
            <ul class="mt-8 space-y-3 text-sm text-white/90" role="list">
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15 ring-1 ring-white/20">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 8h16M4 16h16M8 4v4m8-4v4M8 16v4m8-4v4"/></svg>
                    </span>
                    Wedding, pre-wedding &amp; candid photography
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15 ring-1 ring-white/20">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M15 10l4.55-2.27A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.89L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </span>
                    Cinematic films &amp; music video shoots
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15 ring-1 ring-white/20">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 22s8-4 8-10V7l-8-4-8 4v5c0 6 8 10 8 10z"/></svg>
                    </span>
                    Client galleries, bookings &amp; studio CRM
                </li>
            </ul>
        </div>
    </aside>

    <section class="flex min-h-full flex-col bg-white">
        <div class="mx-auto flex w-full max-w-[440px] flex-1 flex-col justify-center px-6 py-10 sm:px-8">
            <header class="mb-8">
                <a href="/" class="mb-6 inline-flex lg:hidden">
                    <x-brand-logo class="h-10 max-w-[200px]" />
                </a>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0f2744]">@yield('heading', 'Welcome back')</h1>
                <p class="mt-2 text-sm text-slate-500">@yield('subheading', 'Sign in to your studio dashboard or client gallery.')</p>
            </header>
            {{ $slot ?? '' }}
            @yield('content')
        </div>
        <p class="px-6 pb-8 text-center text-sm text-slate-500">
            <a href="/" class="inline-flex items-center gap-1.5 transition hover:text-[#9b7b4b]">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
                Back to Unik Studio
            </a>
        </p>
    </section>
</div>
</body>
</html>
