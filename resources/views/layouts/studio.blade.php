<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $title ?? 'Studio' }} · Lumina</title>
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:600|outfit:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#efe7dc] font-[Outfit] text-[#16120f]">
<div class="flex min-h-screen">
    <aside class="hidden w-64 shrink-0 border-r border-[#16120f]/10 bg-[#16120f] text-[#f6f1ea] md:flex md:flex-col">
        <div class="px-6 py-6 font-[Cormorant_Garamond] text-2xl tracking-[0.2em]">LUMINA</div>
        <nav class="flex-1 space-y-1 px-3 text-sm">
            @php
            $links = [
                ['app.dashboard','Dashboard'],
                ['app.leads.index','Leads'],
                ['app.leads.pipeline','Pipeline'],
                ['app.clients.index','Clients'],
                ['app.consultations.index','Consultations'],
                ['app.packages.index','Packages'],
                ['app.projects.index','Projects'],
                ['app.calendar','Calendar'],
                ['app.team.index','Team'],
                ['app.tasks.index','Tasks'],
                ['app.payments.index','Payments'],
                ['app.invoices.index','Invoices'],
                ['app.galleries.index','Galleries'],
                ['app.files.index','Files'],
                ['app.messages.index','Messages'],
                ['app.reports.index','Reports'],
                ['app.settings.index','Settings'],
            ];
            @endphp
            @foreach ($links as [$route,$label])
                <a href="{{ route($route) }}" class="block rounded-lg px-3 py-2 hover:bg-white/10 {{ request()->routeIs($route) ? 'bg-white/10 text-[#c4a574]' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="p-4 text-sm opacity-70">
            @csrf
            <div class="mb-2">{{ auth()->user()->name }}</div>
            <button type="submit">Sign out</button>
        </form>
    </aside>
    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-20 flex items-center justify-between border-b border-[#16120f]/10 bg-[#efe7dc]/80 px-4 py-3 backdrop-blur md:px-8">
            <livewire:studio.search />
            <a href="{{ route('client.dashboard') }}" class="hidden text-sm md:inline">Client view</a>
        </header>
        <div class="px-4 py-6 md:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-2xl bg-[#16120f] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@livewireScripts
</body>
</html>
