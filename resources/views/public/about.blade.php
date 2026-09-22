@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('about.eyebrow') }}</p>
    <h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">{{ site('about.heading') }}</h1>
    <div class="cms-rich mt-8 max-w-3xl text-lg leading-relaxed text-[#16120f]/75">{!! site_rich('about.body') !!}</div>
    <div class="mt-16 grid gap-8 md:grid-cols-4">
        @foreach ([1,2,3,4] as $n)
            <div>
                <p class="font-[Cormorant_Garamond] text-5xl text-[#9b7b4b]">{{ site('home.stat_'.$n.'_value') }}</p>
                <p class="mt-2 text-sm uppercase tracking-[0.2em] opacity-60">{{ site('home.stat_'.$n.'_label') }}</p>
            </div>
        @endforeach
    </div>
    <h2 class="mt-20 font-[Cormorant_Garamond] text-4xl">{{ site('about.values_heading') }}</h2>
    <div class="mt-8 grid gap-6 md:grid-cols-2">
        <div class="cms-rich rounded-[28px] bg-white p-8 leading-relaxed">{!! site_rich('about.mission') !!}</div>
        <div class="cms-rich rounded-[28px] bg-white p-8 leading-relaxed">{!! site_rich('about.vision') !!}</div>
    </div>
    <h2 class="mt-20 font-[Cormorant_Garamond] text-4xl">{{ site('about.team_heading') }}</h2>
    <div class="mt-8 grid gap-6 md:grid-cols-3">
        @foreach ($team as $member)
            <article class="rounded-[28px] bg-white p-8">
                <h3 class="font-[Cormorant_Garamond] text-3xl">{{ $member->name }}</h3>
                <p class="mt-2 text-sm opacity-60">{{ $member->role }}</p>
            </article>
        @endforeach
    </div>
</section>
@endsection
