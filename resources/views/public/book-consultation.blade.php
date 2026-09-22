@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
<p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">Lumina</p>
<h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">Consultation</h1>
<p class="mt-4 max-w-2xl text-lg opacity-70">Choose a time. Double-booking is prevented in the studio calendar.</p>

@if (session('consultation_success'))
<div class="mt-8 rounded-[28px] bg-[#16120f] p-8 text-white">Your consultation is reserved. A confirmation email is queued.</div>
@else
<form method="POST" action="{{ route('consultation.store') }}" class="mt-8 grid gap-4 rounded-[28px] bg-white p-8">
@csrf
<input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
<label class="text-sm">Name<input required name="name" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3"></label>
<label class="text-sm">Email<input required type="email" name="email" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3"></label>
<label class="text-sm">Phone<input required name="phone" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3"></label>
<label class="text-sm">Available slot
<select required name="slot_id" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
@foreach (\App\Models\ConsultationSlot::withoutTenant()->where('is_available', true)->whereDate('date', '>=', now())->orderBy('date')->get() as $slot)
<option value="{{ $slot->id }}">{{ $slot->date->toFormattedDateString() }} · {{ $slot->start_time }}</option>
@endforeach
</select>
</label>
<label class="text-sm">Notes<textarea name="notes" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3"></textarea></label>
<button class="rounded-full bg-[#16120f] px-6 py-3 text-white">Reserve</button>
</form>
@endif
</section>
@endsection
