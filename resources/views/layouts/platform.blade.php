<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Platform · Unik Studio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-100 font-[Outfit] text-slate-900">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-semibold">Super Admin</h1>
            <nav class="flex gap-4 text-sm">
                <a href="{{ route('platform.dashboard') }}">Studios</a>
                <a href="{{ route('platform.website') }}" class="font-medium">Website content</a>
                <a href="{{ route('app.dashboard') }}">Open studio</a>
                <a href="/" target="_blank">View site</a>
            </nav>
        </div>
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
