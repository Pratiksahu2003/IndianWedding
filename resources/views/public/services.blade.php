@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('services.eyebrow') }}</p>
    <h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">{{ site('services.heading') }}</h1>
    <div class="cms-rich mt-6 max-w-3xl text-lg leading-relaxed text-[#16120f]/75">{!! site_rich('services.intro') !!}</div>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="/packages" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10 transition hover:bg-[#f6f1ea]">Wedding packages & pricing →</a>
        <a href="/production" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10 transition hover:bg-[#f6f1ea]">Production & films →</a>
    </div>

    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($packages as $package)
            <x-public.package-card :package="$package" :show-price="false" :feature-limit="4" />
        @empty
            <p class="col-span-full text-sm opacity-60">Services will appear here once added in the studio.</p>
        @endforelse
    </div>

    <ul class="mt-16 grid gap-3 md:grid-cols-2">
        @foreach ([1,2,3,4,5] as $n)
            @if (site('services.why_'.$n))
                <li class="rounded-2xl bg-white px-5 py-4 text-sm shadow-sm ring-1 ring-black/5">{{ site('services.why_'.$n) }}</li>
            @endif
        @endforeach
    </ul>
</section>
@endsection
