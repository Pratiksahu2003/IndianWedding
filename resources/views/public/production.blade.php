@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('production.eyebrow', 'Production & Creative Services') }}</p>
    <h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">{{ site('production.heading', 'Production & Creative Services') }}</h1>
    <p class="mt-4 max-w-3xl text-lg opacity-70">{{ site('production.intro') }}</p>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="/packages" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10 transition hover:bg-[#f6f1ea]">Wedding packages & pricing →</a>
        <a href="/services" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10 transition hover:bg-[#f6f1ea]">All services →</a>
    </div>

    @if ($productionProjects->isNotEmpty())
        <div class="mt-14">
            <h2 class="font-[Cormorant_Garamond] text-3xl">Featured projects</h2>
            <p class="mt-2 text-sm opacity-60">Real wedding films, music videos and event productions from our portfolio.</p>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($productionProjects as $item)
                    <x-public.production-project-card :project="$item" />
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-20">
        <h2 class="font-[Cormorant_Garamond] text-4xl">{{ site('production.services_heading', 'What we produce') }}</h2>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($packages as $package)
                <x-public.package-card :package="$package" :show-price="false" :feature-limit="5" />
            @empty
                <p class="col-span-full text-sm opacity-60">Production packages will appear here once added in the studio.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-20">
        <h2 class="font-[Cormorant_Garamond] text-3xl">{{ site('production.team_heading', 'Production Team') }}</h2>
        <ul class="mt-6 grid gap-3 sm:grid-cols-2">
            @foreach (range(1, 9) as $n)
                @if (site('production.team_'.$n))
                    <li class="rounded-2xl bg-white px-5 py-4 text-sm shadow-sm ring-1 ring-black/5">{{ site('production.team_'.$n) }}</li>
                @endif
            @endforeach
        </ul>
    </div>

    <div class="mt-16">
        <h2 class="font-[Cormorant_Garamond] text-3xl">{{ site('production.workflow_heading', 'Production Workflow') }}</h2>
        <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-5">
            @foreach (range(1, 5) as $n)
                @if (site('production.step_'.$n.'_title'))
                    <article class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">{{ str_pad((string) $n, 2, '0', STR_PAD_LEFT) }}</p>
                        <h3 class="mt-2 font-[Cormorant_Garamond] text-xl">{{ site('production.step_'.$n.'_title') }}</h3>
                        <p class="mt-2 text-sm opacity-70">{{ site('production.step_'.$n.'_body') }}</p>
                    </article>
                @endif
            @endforeach
        </div>
    </div>

    <div class="mt-16 rounded-[28px] bg-[#16120f] px-8 py-10 text-center text-white">
        <p class="font-[Cormorant_Garamond] text-3xl text-[#e2c48a]">{{ site('production.tagline', 'Your Story. Our Lens. Forever.') }}</p>
        <p class="mx-auto mt-4 max-w-2xl text-sm text-white/75">{{ site('production.tagline_body') }}</p>
        <a href="/book-consultation" class="mt-8 inline-block rounded-full bg-[#c4a574] px-8 py-3 text-[#16120f]">{{ site('home.cta', 'MAKE RESERVATION') }}</a>
    </div>
</section>
@endsection
