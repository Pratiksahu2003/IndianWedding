@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
<p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">Lumina</p>
<h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">Kind words</h1>
<p class="mt-4 max-w-2xl text-lg opacity-70">Notes from couples we photographed this season.</p>

<div class="mt-10 grid gap-6 md:grid-cols-2">
@foreach ($testimonials as $item)
<blockquote class="rounded-[28px] bg-white p-8">
<p class="font-[Cormorant_Garamond] text-2xl">“{{ $item->quote }}”</p>
<footer class="mt-4 text-sm opacity-70">{{ $item->author }} · {{ $item->role }}</footer>
</blockquote>
@endforeach
</div>
</section>
@endsection
