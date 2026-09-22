@extends('layouts.public')

@section('content')
<section class="relative overflow-hidden">
    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1800&q=80" alt="Wedding portrait in golden light" class="h-[88vh] w-full object-cover" />
    <div class="absolute inset-0 bg-gradient-to-t from-[#16120f] via-[#16120f]/30 to-transparent"></div>
    <div class="absolute inset-x-0 bottom-0 mx-auto max-w-6xl px-6 pb-20 text-white" data-reveal>
        <p class="text-sm uppercase tracking-[0.3em] text-[#c4a574]">Lumina Atelier</p>
        <h1 class="mt-4 max-w-3xl font-[Cormorant_Garamond] text-5xl leading-tight md:text-7xl">Photographs with the quiet of candlelight.</h1>
        <p class="mt-4 max-w-xl text-white/80">Editorial wedding photography, films, and albums for modern celebrations.</p>
        <div class="mt-8 flex flex-wrap gap-4">
            <a href="/book-consultation" class="rounded-full bg-white px-6 py-3 text-[#16120f]">Book a consultation</a>
            <a href="/packages" class="rounded-full border border-white/40 px-6 py-3">View collections</a>
        </div>
    </div>
</section>

<section class="mx-auto grid max-w-6xl gap-10 px-6 py-24 md:grid-cols-3" data-scroll>
    <div>
        <p class="font-[Cormorant_Garamond] text-6xl text-[#9b7b4b]">48</p>
        <p class="mt-2 text-sm uppercase tracking-[0.2em] text-[#16120f]/60">Weddings archived</p>
    </div>
    <div>
        <p class="font-[Cormorant_Garamond] text-6xl text-[#9b7b4b]">12</p>
        <p class="mt-2 text-sm uppercase tracking-[0.2em] text-[#16120f]/60">Cities</p>
    </div>
    <div>
        <p class="font-[Cormorant_Garamond] text-6xl text-[#9b7b4b]">4.9</p>
        <p class="mt-2 text-sm uppercase tracking-[0.2em] text-[#16120f]/60">Client rating</p>
    </div>
</section>

<section class="mx-auto max-w-6xl px-6" data-scroll>
    <div class="flex items-end justify-between">
        <h2 class="font-[Cormorant_Garamond] text-4xl">Collections</h2>
        <a href="/packages" class="text-sm">All packages</a>
    </div>
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        @forelse ($packages as $package)
            <article class="rounded-[28px] bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <h3 class="font-[Cormorant_Garamond] text-3xl">{{ $package->name }}</h3>
                <p class="mt-3 text-sm text-[#16120f]/70">{{ $package->description }}</p>
                <p class="mt-6 text-lg">{{ \App\Support\Money::format($package->price) }}</p>
            </article>
        @empty
            <div class="rounded-[28px] bg-white p-8">Collections will appear once the studio publishes packages.</div>
        @endforelse
    </div>
</section>

<section class="mx-auto mt-24 max-w-6xl px-6" data-scroll>
    <h2 class="font-[Cormorant_Garamond] text-4xl">Selected work</h2>
    <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-4">
        @forelse ($portfolio as $item)
            <figure class="overflow-hidden rounded-3xl">
                <img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="h-56 w-full object-cover transition duration-700 hover:scale-105" />
            </figure>
        @empty
            <p class="col-span-4 text-sm opacity-70">Portfolio images will appear after seeding.</p>
        @endforelse
    </div>
</section>

<section class="mx-auto mt-24 max-w-3xl px-6 pb-24" data-scroll>
    @include('public.partials.inquiry-form')
</section>
@endsection
