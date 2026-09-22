@extends('layouts.public')
@section('content')
<article>
    <section class="relative overflow-hidden">
        <img src="{{ $package->cover_image ?: \App\Support\UnikStudioAssets::url('hero-slide.png') }}" alt="{{ $package->name }}" class="h-[70vh] w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-[#16120f] via-[#16120f]/40 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 mx-auto max-w-6xl px-6 pb-14 text-white">
            <a href="/services" class="text-sm text-white/70">← All services</a>
            <p class="mt-4 text-sm uppercase tracking-[0.3em] text-[#c4a574]">{{ site('brand.name', 'Unik Studio') }}</p>
            <h1 class="mt-3 max-w-4xl font-[Cormorant_Garamond] text-4xl italic text-balance sm:text-5xl md:text-6xl xl:text-7xl">{{ $package->name }}</h1>
        </div>
    </section>

    <section class="mx-auto grid max-w-6xl gap-12 px-6 py-20 lg:grid-cols-[1.4fr_0.8fr]">
        <div>
            <p class="text-lg leading-relaxed text-[#16120f]/80">{{ $package->description }}</p>
            @if ($package->body)
                <div class="mt-8 space-y-4 text-base leading-relaxed text-[#16120f]/75">
                    @foreach (preg_split('/\n\s*\n/', trim($package->body)) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            @endif
            @if ($package->items->isNotEmpty())
                <h2 class="mt-12 font-[Cormorant_Garamond] text-3xl">What’s included</h2>
                <ul class="mt-6 grid gap-3">
                    @foreach ($package->items as $item)
                        <li class="rounded-2xl bg-white px-5 py-4">{{ $item->name }}{{ $item->description ? ' — '.$item->description : '' }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="mt-10 grid gap-4 sm:grid-cols-2">
                @foreach ([1,2,3,4,5] as $n)
                    @if (site('services.why_'.$n))
                        <p class="rounded-2xl bg-white px-5 py-4 text-sm">{{ site('services.why_'.$n) }}</p>
                    @endif
                @endforeach
            </div>
        </div>
        <aside class="h-fit rounded-[28px] bg-white p-8 shadow-sm">
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">Starting at</p>
            <p class="mt-2 font-[Cormorant_Garamond] text-4xl">{{ \App\Support\Money::format($package->price) }}</p>
            <ul class="mt-6 space-y-2 text-sm text-[#16120f]/70">
                <li>{{ $package->duration_hours }} hours coverage</li>
                <li>{{ $package->photographer_count }} photographer{{ $package->photographer_count != 1 ? 's' : '' }}</li>
                <li>{{ $package->videographer_count }} videographer{{ $package->videographer_count != 1 ? 's' : '' }}</li>
                <li>{{ $package->edited_photos }} edited photos</li>
                @if ($package->includes_album)<li>Premium album</li>@endif
                @if ($package->includes_video)<li>Cinematic film</li>@endif
                @if ($package->includes_pre_wedding)<li>Pre-wedding session</li>@endif
                @if ($package->includes_drone)<li>Drone coverage</li>@endif
            </ul>
            <a href="/book-consultation" class="mt-8 inline-block w-full rounded-full bg-[#16120f] px-5 py-3 text-center text-white">{{ site('home.cta', 'MAKE RESERVATION') }}</a>
            <a href="/packages" class="mt-3 inline-block w-full text-center text-sm text-[#9b7b4b]">View pricing page</a>
        </aside>
    </section>

    @if ($gallery->isNotEmpty())
        <section class="mx-auto max-w-6xl px-6 pb-12">
            <h2 class="font-[Cormorant_Garamond] text-4xl">From this service</h2>
            <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-4">
                @foreach ($gallery as $shot)
                    <a href="{{ $shot->publicUrl() }}" class="overflow-hidden rounded-3xl">
                        <img src="{{ $shot->image_path }}" alt="{{ $shot->title }}" class="h-56 w-full object-cover transition duration-700 hover:scale-105">
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-6xl px-6 py-12">
            <h2 class="font-[Cormorant_Garamond] text-4xl">Other services</h2>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach ($related as $other)
                    <a href="{{ $other->publicUrl() }}" class="rounded-[28px] bg-white p-8 transition hover:-translate-y-1 hover:shadow-xl">
                        <h3 class="font-[Cormorant_Garamond] text-2xl">{{ $other->name }}</h3>
                        <p class="mt-3 line-clamp-3 text-sm text-[#16120f]/70">{{ $other->description }}</p>
                        <p class="mt-4 text-sm text-[#9b7b4b]">Read more →</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-3xl px-6 pb-24">
        @include('public.partials.inquiry-form')
    </section>
</article>
@endsection
