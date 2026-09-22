@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
<p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">Lumina</p>
<h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">Portfolio</h1>
<p class="mt-4 max-w-2xl text-lg opacity-70">Selected frames from recent weddings.</p>

<div class="mt-10 grid grid-cols-2 gap-3 md:grid-cols-3">
@foreach ($portfolio as $item)
<img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="h-64 w-full rounded-3xl object-cover" />
@endforeach
</div>
</section>
@endsection
