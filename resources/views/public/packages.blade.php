@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
<p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">Lumina</p>
<h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">Collections</h1>
<p class="mt-4 max-w-2xl text-lg opacity-70">Transparent packages. Add-ons remain optional.</p>

<div class="mt-10 grid gap-6 md:grid-cols-3">
@forelse ($packages as $package)
<article class="rounded-[28px] bg-white p-8">
<h2 class="font-[Cormorant_Garamond] text-3xl">{{ $package->name }}</h2>
<p class="mt-3 text-sm opacity-70">{{ $package->description }}</p>
<p class="mt-4">{{ \App\Support\Money::format($package->price) }}</p>
<ul class="mt-4 space-y-1 text-sm opacity-80">
<li>{{ $package->photographer_count }} photographers · {{ $package->videographer_count }} videographers</li>
<li>{{ $package->edited_photos }} edited photographs</li>
<li>{{ $package->duration_hours }} hours of coverage</li>
</ul>
</article>
@empty
<p>No public packages yet.</p>
@endforelse
</div>
</section>
@endsection
