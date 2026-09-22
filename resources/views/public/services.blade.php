@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('services.eyebrow') }}</p>
    <h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">{{ site('services.heading') }}</h1>
    <div class="cms-rich mt-6 max-w-3xl text-lg leading-relaxed text-[#16120f]/75">{!! site_rich('services.intro') !!}</div>
    <p class="mt-8 text-sm uppercase tracking-[0.2em] opacity-50">{{ site('services.featured_date') }}</p>
    <p class="mt-3 max-w-3xl text-sm opacity-70">{{ site('services.featured_blurb') }}</p>
    <div class="mt-12 grid gap-6 md:grid-cols-2">
        @foreach ($packages as $package)
            <article class="rounded-[28px] bg-white p-8">
                <h2 class="font-[Cormorant_Garamond] text-3xl"><a href="{{ $package->publicUrl() }}">{{ $package->name }}</a></h2>
                <p class="mt-4 leading-relaxed text-[#16120f]/75">{{ $package->description }}</p>
                <ul class="mt-4 space-y-1 text-sm opacity-70">
                    @foreach ($package->items as $item)
                        <li>{{ $item->name }}</li>
                    @endforeach
                </ul>
                <a href="{{ $package->publicUrl() }}" class="mt-6 inline-block text-sm text-[#9b7b4b]">Open detailed page →</a>
            </article>
        @endforeach
    </div>
    <ul class="mt-12 grid gap-3 md:grid-cols-2">
        @foreach ([1,2,3,4,5] as $n)
            <li class="rounded-2xl bg-white px-5 py-4">{{ site('services.why_'.$n) }}</li>
        @endforeach
    </ul>
</section>
@endsection
