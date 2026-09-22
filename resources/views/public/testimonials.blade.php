@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('home.feedbacks_kicker') }}</p>
    <h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">{{ site('home.feedbacks_heading') }}</h1>
    <div class="mt-10 grid gap-6 md:grid-cols-2">
        @foreach ($testimonials as $item)
            <blockquote class="rounded-[28px] bg-white p-8">
                <div class="cms-rich font-[Cormorant_Garamond] text-2xl">“{!! rich_html($item->quote) !!}”</div>
                <footer class="mt-4 text-sm opacity-70">{{ $item->author }} · {{ $item->role }}</footer>
            </blockquote>
        @endforeach
    </div>
</section>
@endsection
