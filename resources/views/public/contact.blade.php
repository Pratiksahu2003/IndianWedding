@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <h1 class="font-[Cormorant_Garamond] text-5xl">{{ site('contact.heading') }}</h1>
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        <article class="rounded-[28px] bg-white p-8">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">{{ site('contact.office_label') }}</h2>
            <p class="mt-3 leading-relaxed">{{ site('contact.office') }}</p>
        </article>
        <article class="rounded-[28px] bg-white p-8">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">{{ site('contact.email_label') }}</h2>
            <p class="mt-3 whitespace-pre-line">{{ site('contact.emails') }}</p>
        </article>
        <article class="rounded-[28px] bg-white p-8">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">{{ site('contact.phone_label') }}</h2>
            <p class="mt-3">{{ site('contact.phone_note') }}</p>
            <p class="mt-1 text-xl">{{ site('contact.phone') }}</p>
        </article>
    </div>
    <div class="mt-12 rounded-[28px] bg-white p-8">
        <h2 class="font-[Cormorant_Garamond] text-3xl">{{ site('contact.subscribe_heading') }}</h2>
        <p class="mt-2 text-sm opacity-70">{{ site('contact.subscribe_note') }}</p>
    </div>
    <div class="mt-12 max-w-3xl">
        @include('public.partials.inquiry-form')
    </div>
</section>
@endsection
