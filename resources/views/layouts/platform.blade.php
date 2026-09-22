<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Platform · Lumina</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-100 font-[Outfit] text-slate-900">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Platform admin</h1>
            <a href="{{ route('app.dashboard') }}">Open studio</a>
        </div>
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
