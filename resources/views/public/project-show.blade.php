@extends('layouts.public')
@section('content')
<article>
    <section class="relative overflow-hidden">
        <img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="h-[78vh] w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-[#16120f] via-[#16120f]/35 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 mx-auto max-w-6xl px-6 pb-14 text-white">
            <a href="/portfolio" class="text-sm text-white/70">← All projects</a>
            <p class="mt-4 text-sm uppercase tracking-[0.3em] text-[#c4a574]">{{ $item->category ?: 'Portfolio' }}{{ $item->location ? ' · '.$item->location : '' }}</p>
            <h1 class="mt-3 max-w-4xl font-[Cormorant_Garamond] text-4xl italic text-balance sm:text-5xl md:text-6xl xl:text-7xl">{{ $item->title }}</h1>
            @if ($item->couple)
                <p class="mt-4 text-lg text-white/80">{{ $item->couple }}</p>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-6 py-20">
        @if ($item->event_date)
            <p class="text-sm uppercase tracking-[0.2em] text-[#9b7b4b]">{{ $item->event_date->toFormattedDateString() }}</p>
        @endif
        <div class="mt-6 space-y-4 text-lg leading-relaxed text-[#16120f]/80">
            @forelse (preg_split('/\n\s*\n/', trim((string) $item->story)) as $paragraph)
                @if ($paragraph !== '')
                    <p>{{ $paragraph }}</p>
                @endif
            @empty
                <p>{{ $item->title }} — a Unik Studio film and photograph story from {{ $item->location ?: 'New Delhi' }}.</p>
            @endforelse
        </div>
        <a href="/book-consultation" class="mt-10 inline-block rounded-full bg-[#16120f] px-6 py-3 text-white">{{ site('home.cta', 'MAKE RESERVATION') }}</a>
    </section>

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-6xl px-6 pb-8">
            <h2 class="font-[Cormorant_Garamond] text-4xl">More projects</h2>
            <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-3">
                @foreach ($related as $other)
                    <a href="{{ $other->publicUrl() }}" class="group overflow-hidden rounded-3xl">
                        <img src="{{ $other->image_path }}" alt="{{ $other->title }}" class="h-64 w-full object-cover transition duration-700 group-hover:scale-105">
                        <p class="mt-2 text-sm">{{ $other->title }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($services->isNotEmpty())
        <section class="mx-auto max-w-6xl px-6 py-16">
            <h2 class="font-[Cormorant_Garamond] text-4xl">Book this look</h2>
            <div class="mt-8 grid gap-6 md:grid-cols-4">
                @foreach ($services as $package)
                    <a href="{{ $package->publicUrl() }}" class="rounded-[28px] bg-white p-6">
                        <h3 class="font-[Cormorant_Garamond] text-2xl">{{ $package->name }}</h3>
                        <p class="mt-2 text-sm text-[#9b7b4b]">{{ \App\Support\Money::format($package->price) }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</article>
@endsection
