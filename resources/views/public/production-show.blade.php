@extends('layouts.public')
@section('content')
<article>
    <section class="relative overflow-hidden">
        @if ($project->images->count() > 1)
            <div class="grid h-[70vh] grid-cols-2 gap-1 md:grid-cols-3">
                @foreach ($project->images->take(6) as $image)
                    <img src="{{ $image->url() }}" alt="{{ $project->name }}" class="h-full w-full object-cover">
                @endforeach
            </div>
        @else
            <img src="{{ $project->coverUrl() }}" alt="{{ $project->name }}" class="h-[70vh] w-full object-cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-[#16120f] via-[#16120f]/40 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 mx-auto max-w-6xl px-6 pb-14 text-white">
            <a href="/production" class="text-sm text-white/70">← All production</a>
            <p class="mt-4 text-sm uppercase tracking-[0.3em] text-[#c4a574]">{{ str_replace('-', ' ', $project->category ?? 'Production') }}</p>
            <h1 class="mt-3 max-w-4xl font-[Cormorant_Garamond] text-4xl italic sm:text-5xl md:text-6xl">{{ $project->name }}</h1>
            @if ($project->subtitle)
                <p class="mt-3 max-w-2xl text-lg text-white/75">{{ $project->subtitle }}</p>
            @endif
        </div>
    </section>

    <section class="mx-auto grid max-w-6xl gap-12 px-6 py-20 lg:grid-cols-[1.4fr_0.8fr]">
        <div>
            @if ($project->description)
                <div class="text-lg leading-relaxed text-[#16120f]/80">{!! rich_html($project->description) !!}</div>
            @endif
            @if ($project->body)
                <div class="mt-8 space-y-4 text-base leading-relaxed text-[#16120f]/75">
                    @foreach (preg_split('/\n\s*\n/', trim($project->body)) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            @endif
            @if ($project->hasYoutubeVideo())
                <div class="mt-10">
                    <h2 class="font-[Cormorant_Garamond] text-3xl">Watch the film</h2>
                    <x-youtube-embed :url="$project->youtube_url" :title="$project->name" class="mt-6" />
                </div>
            @endif
            @if ($project->images->count() > 1)
                <div class="mt-12">
                    <h2 class="font-[Cormorant_Garamond] text-3xl">Gallery</h2>
                    <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-3">
                        @foreach ($project->images as $image)
                            <img src="{{ $image->url() }}" alt="{{ $project->name }}" class="h-48 w-full rounded-2xl object-cover">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <aside class="h-fit rounded-[28px] bg-white p-8 shadow-sm">
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">Project details</p>
            <ul class="mt-6 space-y-3 text-sm text-[#16120f]/70">
                @if ($project->client_name)<li><span class="text-[#16120f]/45">Client</span><br>{{ $project->client_name }}</li>@endif
                @if ($project->location)<li><span class="text-[#16120f]/45">Location</span><br>{{ $project->location }}</li>@endif
                @if ($project->project_date)<li><span class="text-[#16120f]/45">Date</span><br>{{ $project->project_date->toFormattedDateString() }}</li>@endif
                <li><span class="text-[#16120f]/45">Category</span><br>{{ str_replace('-', ' ', $project->category ?? 'Production') }}</li>
            </ul>
            <a href="/book-consultation" class="mt-8 inline-block w-full rounded-full bg-[#16120f] px-5 py-3 text-center text-white">{{ site('home.cta', 'BOOK CONSULTANT') }}</a>
        </aside>
    </section>

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-6xl px-6 pb-24">
            <h2 class="font-[Cormorant_Garamond] text-4xl">More production</h2>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach ($related as $other)
                    <a href="{{ $other->publicUrl() }}" class="overflow-hidden rounded-[28px] bg-white transition hover:-translate-y-1 hover:shadow-xl">
                        <img src="{{ $other->coverUrl() }}" alt="{{ $other->name }}" class="h-48 w-full object-cover">
                        <div class="p-6">
                            <h3 class="font-[Cormorant_Garamond] text-2xl">{{ $other->name }}</h3>
                            <p class="mt-2 text-sm text-[#9b7b4b]">View project →</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</article>
@endsection
