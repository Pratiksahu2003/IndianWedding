@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
<p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">Lumina</p>
<h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">Services</h1>
<p class="mt-4 max-w-2xl text-lg opacity-70">Photography, cinematography, albums, pre-wedding, and drone coverage — scoped to the day, never padded.</p>

<div class="mt-10 grid gap-6 md:grid-cols-3">
@foreach (['Wedding day','Pre-wedding','Films','Albums','Drone','Family coverage'] as $service)
<div class="rounded-[28px] bg-white p-8">
<h2 class="font-[Cormorant_Garamond] text-3xl">{{ $service }}</h2>
<p class="mt-3 text-sm opacity-70">Scoped coverage, a dedicated producer, and a defined delivery timeline.</p>
</div>
@endforeach
</div>
</section>
@endsection
