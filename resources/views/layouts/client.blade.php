<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $title ?? 'Your wedding' }}</title>
    @include('partials.brand-head')
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:600,700|outfit:300,400,500" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#16120f] font-[Outfit] text-[#f6f1ea]">
    @php
        $navProject = \App\Models\Customer::query()->where('user_id', auth()->id())->first()?->projects()->latest()->first();
        $clientNav = array_filter([
            ['client.dashboard', 'Home', request()->routeIs('client.dashboard')],
            $navProject ? ['client.project', 'Project', request()->routeIs('client.project'), $navProject] : null,
            ['client.timeline', 'Timeline', request()->routeIs('client.timeline')],
            ['client.payments', 'Payments', request()->routeIs('client.payments')],
            ['client.gallery', 'Gallery', request()->routeIs('client.gallery')],
        ]);
    @endphp
    <header class="sticky top-0 z-30 border-b border-white/10 bg-[#16120f]/95 px-4 py-4 backdrop-blur-xl md:px-6" x-data="{ open: false }">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4">
            <a href="{{ route('client.dashboard') }}" class="inline-flex shrink-0 items-center">
                <x-brand-logo class="h-9 max-w-[160px] md:h-10 md:max-w-[180px]" />
            </a>
            <button type="button" class="rounded-xl p-2 text-white/70 ring-1 ring-white/10 md:hidden" @click="open=!open" aria-label="Menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
            <nav class="hidden items-center gap-1 text-sm md:flex">
                @foreach ($clientNav as $item)
                    @php [$route, $label, $active] = $item; $param = $item[3] ?? null; @endphp
                    <a href="{{ route($route, $param ?? []) }}" class="rounded-lg px-3 py-2 transition {{ $active ? 'bg-white/10 text-[#e2c48a]' : 'text-white/65 hover:bg-white/5 hover:text-white' }}">{{ $label }}</a>
                @endforeach
                <form method="POST" action="{{ route('logout') }}" class="ml-2">@csrf<button class="rounded-lg px-3 py-2 text-white/50 transition hover:text-white">Sign out</button></form>
            </nav>
        </div>
        <nav x-show="open" x-cloak @click.outside="open=false" class="mx-auto mt-3 flex max-w-5xl flex-col gap-1 border-t border-white/10 pt-3 md:hidden">
            @foreach ($clientNav as $item)
                @php [$route, $label, $active] = $item; $param = $item[3] ?? null; @endphp
                <a href="{{ route($route, $param ?? []) }}" @click="open=false" class="rounded-xl px-3 py-2.5 {{ $active ? 'bg-white/10 text-[#e2c48a]' : 'text-white/70' }}">{{ $label }}</a>
            @endforeach
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full rounded-xl px-3 py-2.5 text-left text-white/50">Sign out</button></form>
        </nav>
    </header>
    <main class="px-6 pb-16">
        <x-swal-flash />
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
