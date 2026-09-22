@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
<p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">Lumina</p>
<h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">Questions</h1>
<p class="mt-4 max-w-2xl text-lg opacity-70">Straightforward answers before you write to us.</p>

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
