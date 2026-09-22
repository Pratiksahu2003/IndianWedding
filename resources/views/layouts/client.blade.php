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
    <header class="flex items-center justify-between px-6 py-5">
        <a href="{{ route('client.dashboard') }}" class="inline-flex items-center">
            <x-brand-logo class="h-10 max-w-[180px]" />
        </a>
        <nav class="flex gap-5 text-sm text-white/70">
            <a href="{{ route('client.dashboard') }}">Home</a>
            @php
                $navProject = \App\Models\Customer::query()->where('user_id', auth()->id())->first()?->projects()->latest()->first();
            @endphp
            @if ($navProject)
                <a href="{{ route('client.project', $navProject) }}">Project</a>
            @endif
            <a href="{{ route('client.timeline') }}">Timeline</a>
            <a href="{{ route('client.payments') }}">Payments</a>
            <a href="{{ route('client.gallery') }}">Gallery</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button>Sign out</button></form>
        </nav>
    </header>
    <main class="px-6 pb-16">
        <x-swal-flash />
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
