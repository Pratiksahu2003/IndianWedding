@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <h1 class="font-[Cormorant_Garamond] text-5xl">{{ site('faq.heading', site('nav.faq', 'FAQ')) }}</h1>
    @if (site('faq.intro'))
        <p class="mt-4 max-w-2xl text-lg opacity-70">{{ site('faq.intro') }}</p>
    @endif
    <div class="mt-10 space-y-4">
        @foreach ($faqs as $faq)
            <details class="rounded-2xl bg-white p-6">
                <summary class="cursor-pointer font-medium">{{ $faq->question }}</summary>
                <div class="cms-rich mt-2 text-sm opacity-70">{!! rich_html($faq->answer) !!}</div>
            </details>
        @endforeach
    </div>
</section>
@endsection
