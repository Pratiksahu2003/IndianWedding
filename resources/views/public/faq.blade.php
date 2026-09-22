@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <h1 class="font-[Cormorant_Garamond] text-5xl">{{ site('nav.faq', 'FAQ') }}</h1>
    <div class="mt-10 space-y-4">
        @foreach ($faqs as $faq)
            <details class="rounded-2xl bg-white p-6">
                <summary class="cursor-pointer font-medium">{{ $faq->question }}</summary>
                <p class="mt-2 text-sm opacity-70">{{ $faq->answer }}</p>
            </details>
        @endforeach
    </div>
</section>
@endsection
