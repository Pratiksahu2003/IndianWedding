@extends('layouts.public')

@section('content')
<section class="relative overflow-hidden">
    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1800&q=80" alt="{{ site('brand.name') }}" class="h-[88vh] w-full object-cover" />
    <div class="absolute inset-0 bg-gradient-to-t from-[#16120f] via-[#16120f]/30 to-transparent"></div>
    <div class="absolute inset-x-0 bottom-0 mx-auto max-w-6xl px-6 pb-20 text-white" data-reveal>
        <p class="text-sm uppercase tracking-[0.3em] text-[#c4a574]">{{ site('home.kicker') }}</p>
        <h1 class="mt-4 max-w-4xl font-[Cormorant_Garamond] text-5xl italic leading-tight md:text-7xl">{{ site('home.headline') }}</h1>
        <div class="mt-8">
            <a href="/book-consultation" class="rounded-full bg-white px-6 py-3 text-[#16120f]">{{ site('home.cta') }}</a>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-6 py-24" data-scroll>
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('home.story_kicker') }}</p>
    <h2 class="mt-4 font-[Cormorant_Garamond] text-4xl md:text-5xl">{{ site('home.story_heading') }}</h2>
    <p class="mt-6 max-w-3xl text-lg leading-relaxed text-[#16120f]/75">{{ site('home.story_body') }}</p>
    <a href="/book-consultation" class="mt-8 inline-block rounded-full bg-[#16120f] px-6 py-3 text-white">{{ site('home.story_cta') }}</a>
</section>

<section class="mx-auto max-w-6xl px-6" data-scroll>
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('home.services_kicker') }}</p>
    <h2 class="mt-3 font-[Cormorant_Garamond] text-4xl">{{ site('home.services_heading') }}</h2>
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        @foreach ($packages as $package)
            <article class="rounded-[28px] bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <h3 class="font-[Cormorant_Garamond] text-3xl">{{ $package->name }}</h3>
                <p class="mt-3 text-sm leading-relaxed text-[#16120f]/70">{{ $package->description }}</p>
                <a href="{{ $package->publicUrl() }}" class="mt-4 inline-block text-sm text-[#9b7b4b]">View service →</a>
            </article>
        @endforeach
    </div>
</section>

<section class="mx-auto mt-24 max-w-6xl px-6" data-scroll>
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('home.moments_kicker') }}</p>
    <h2 class="mt-3 font-[Cormorant_Garamond] text-4xl">{{ site('home.moments_heading') }}</h2>
    <div class="mt-10 grid gap-8 md:grid-cols-4">
        @foreach ([1,2,3,4] as $n)
            <div>
                <p class="font-[Cormorant_Garamond] text-6xl text-[#9b7b4b]">{{ site('home.stat_'.$n.'_value') }}</p>
                <p class="mt-2 text-sm uppercase tracking-[0.2em] text-[#16120f]/60">{{ site('home.stat_'.$n.'_label') }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="mx-auto mt-24 max-w-6xl px-6" data-scroll>
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('home.guide_kicker') }}</p>
    <div class="mt-8 grid gap-4 md:grid-cols-2">
        @foreach ([1,2,3,4,5] as $n)
            <p class="rounded-3xl bg-white p-6 text-lg">{{ site('home.guide_'.$n) }}</p>
        @endforeach
    </div>
    <p class="mt-6 text-xs uppercase tracking-[0.25em] opacity-50">{{ site('home.guide_note') }}</p>
</section>

<section class="mx-auto mt-24 max-w-6xl px-6" data-scroll>
    <h2 class="font-[Cormorant_Garamond] text-4xl">{{ site('home.portfolio_heading') }}</h2>
    <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-4">
        @foreach ($portfolio as $item)
            <a href="{{ $item->publicUrl() }}" class="overflow-hidden rounded-3xl">
                <img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="h-56 w-full object-cover transition duration-700 hover:scale-105" />
            </a>
        @endforeach
    </div>
</section>

<section class="mx-auto mt-24 max-w-6xl px-6 text-center" data-scroll>
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('home.rsvp_kicker') }}</p>
    <h2 class="mt-3 font-[Cormorant_Garamond] text-5xl">{{ site('home.rsvp_heading') }}</h2>
    <a href="/book-consultation" class="mt-8 inline-block rounded-full bg-[#16120f] px-8 py-3 text-white">{{ site('home.rsvp_cta') }}</a>
</section>

<section class="mx-auto mt-24 max-w-6xl px-6" data-scroll>
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('home.feedbacks_kicker') }}</p>
    <h2 class="mt-3 font-[Cormorant_Garamond] text-4xl">{{ site('home.feedbacks_heading') }}</h2>
    <div class="mt-10 grid gap-6 md:grid-cols-2">
        @foreach ($testimonials as $item)
            <blockquote class="rounded-[28px] bg-white p-8">
                <p class="font-[Cormorant_Garamond] text-2xl">“{{ $item->quote }}”</p>
                <footer class="mt-4 text-sm opacity-70">{{ $item->author }} · {{ $item->role }}</footer>
            </blockquote>
        @endforeach
    </div>
</section>

<section class="mx-auto mt-16 max-w-3xl px-6 pb-24" data-scroll>
    @include('public.partials.inquiry-form')
</section>
@endsection
