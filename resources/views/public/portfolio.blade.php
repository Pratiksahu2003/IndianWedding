@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <h1 class="font-[Cormorant_Garamond] text-5xl">{{ site('home.portfolio_heading') }}</h1>
    <div class="mt-10 grid grid-cols-2 gap-3 md:grid-cols-3">
        @foreach ($portfolio as $item)
            <a href="{{ $item->publicUrl() }}" class="group">
                <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="h-64 w-full rounded-3xl object-cover transition duration-700 group-hover:scale-105" />
                <p class="mt-2 text-sm">{{ $item->title }}</p>
            </a>
        @endforeach
    </div>
</section>
@endsection
