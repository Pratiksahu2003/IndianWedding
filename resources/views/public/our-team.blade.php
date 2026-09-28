@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <h1 class="font-[Cormorant_Garamond] text-5xl">{{ site('team.heading', site('about.team_heading')) }}</h1>
    @if (site('team.intro'))
        <p class="mt-4 max-w-2xl text-lg opacity-70">{{ site('team.intro') }}</p>
    @endif
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        @foreach ($team as $member)
            <article class="rounded-[28px] bg-white p-8">
                <h2 class="font-[Cormorant_Garamond] text-3xl">{{ $member->name }}</h2>
                <p class="mt-2 text-sm opacity-60">{{ $member->role }}</p>
                @if ($member->bio)<p class="mt-4 text-sm leading-relaxed">{{ $member->bio }}</p>@endif
            </article>
        @endforeach
    </div>
</section>
@endsection
