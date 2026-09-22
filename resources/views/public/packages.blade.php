@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <h1 class="font-[Cormorant_Garamond] text-5xl">{{ site('nav.packages', 'Pricing Page') }}</h1>
    <p class="mt-4 max-w-2xl text-lg opacity-70">{{ site('services.intro') }}</p>
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        @forelse ($packages as $package)
            <article class="rounded-[28px] bg-white p-8">
                <h2 class="font-[Cormorant_Garamond] text-3xl"><a href="{{ $package->publicUrl() }}">{{ $package->name }}</a></h2>
                <p class="mt-3 text-sm opacity-70">{{ $package->description }}</p>
                <p class="mt-4 text-lg">{{ \App\Support\Money::format($package->price) }}</p>
                <ul class="mt-4 space-y-1 text-sm opacity-80">
                    @foreach ($package->items as $item)
                        <li>{{ $item->name }}</li>
                    @endforeach
                </ul>
                <a href="{{ $package->publicUrl() }}" class="mt-6 inline-block text-sm text-[#9b7b4b]">View details</a>
                <a href="/book-consultation" class="mt-6 inline-block rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">{{ site('home.cta') }}</a>
            </article>
        @empty
            <p>No public packages yet.</p>
        @endforelse
    </div>
</section>
@endsection
