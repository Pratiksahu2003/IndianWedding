@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <h1 class="font-[Cormorant_Garamond] text-5xl">{{ site('gallery.heading') }}</h1>
    <p class="mt-4 text-lg opacity-70">{{ site('gallery.intro') }}</p>
    <h2 class="mt-12 font-[Cormorant_Garamond] text-3xl">{{ site('gallery.prewedding_heading') }}</h2>
    <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-4">
        @foreach ($portfolio as $item)
            <a href="{{ $item->publicUrl() }}">
                <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="h-56 w-full rounded-3xl object-cover">
                <figcaption class="mt-2 text-sm opacity-60">{{ $item->title }}</figcaption>
            </a>
        @endforeach
    </div>
</section>
@endsection
