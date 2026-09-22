<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $title ?? 'Sign in' }} · Lumina</title>
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:600|outfit:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#16120f] font-[Outfit] text-[#f6f1ea]">
    <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center px-6">
        <a href="/" class="mb-10 text-center font-[Cormorant_Garamond] text-4xl tracking-[0.25em]">LUMINA</a>
        {{ $slot ?? '' }}
        @yield('content')
    </div>
</body>
</html>
