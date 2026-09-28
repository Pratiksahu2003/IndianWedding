@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('production.eyebrow', 'Film & Production') }}</p>
    <h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">{{ site('production.heading', 'Wedding & Production Films') }}</h1>
    <p class="mt-4 max-w-3xl text-lg opacity-70">{{ site('production.intro') }}</p>

    @if ($productionProjects->isNotEmpty())
        <div class="mt-14 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($productionProjects as $item)
                <a href="{{ $item->publicUrl() }}" class="group overflow-hidden rounded-[28px] bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="relative aspect-[4/3] overflow-hidden">
                        <img src="{{ $item->coverUrl() }}" alt="{{ $item->name }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                        @if ($item->hasYoutubeVideo())
                            <span class="absolute bottom-3 right-3 rounded-full bg-[#16120f]/80 px-3 py-1 text-xs text-white">▶ Film</span>
                        @endif
                    </div>
                    <div class="p-6">
                        <p class="text-xs uppercase tracking-[0.16em] text-[#9b7b4b]">{{ str_replace('-', ' ', $item->category ?? 'Production') }}</p>
                        <h2 class="mt-2 font-[Cormorant_Garamond] text-2xl group-hover:text-[#9b7b4b]">{{ $item->name }}</h2>
                        @if ($item->subtitle)
                            <p class="mt-2 text-sm opacity-60">{{ $item->subtitle }}</p>
                        @endif
                        <p class="mt-4 text-sm text-[#9b7b4b]">View project →</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="mt-20">
        <h2 class="font-[Cormorant_Garamond] text-4xl">{{ site('production.services_heading', 'What we produce') }}</h2>
        <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach (range(1, 6) as $n)
                @if (site('production.service_'.$n.'_title'))
                    <article class="rounded-[28px] bg-white p-6">
                        <h3 class="font-[Cormorant_Garamond] text-xl">{{ site('production.service_'.$n.'_title') }}</h3>
                        <p class="mt-2 text-sm opacity-70">{{ site('production.service_'.$n.'_body') }}</p>
                    </article>
                @endif
            @endforeach
        </div>
    </div>

    <div class="mt-16">
        <h2 class="font-[Cormorant_Garamond] text-3xl">{{ site('production.why_heading', 'Why Unik Studio for production') }}</h2>
        <ul class="mt-6 grid gap-3 sm:grid-cols-2">
            @foreach (range(1, 5) as $n)
                @if (site('production.why_'.$n))
                    <li class="rounded-2xl bg-white px-5 py-4 text-sm">{{ site('production.why_'.$n) }}</li>
                @endif
            @endforeach
        </ul>
    </div>

    <div class="mt-16 text-center">
        <a href="/book-consultation" class="inline-block rounded-full bg-[#16120f] px-8 py-3 text-white">{{ site('home.cta', 'MAKE RESERVATION') }}</a>
    </div>
</section>
@endsection
