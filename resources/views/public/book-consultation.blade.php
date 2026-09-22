@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <h1 class="font-[Cormorant_Garamond] text-5xl">{{ site('reservation.heading') }}</h1>
    <p class="mt-4 max-w-2xl text-lg opacity-70">{{ site('reservation.body') }}</p>
    <p class="mt-4 text-sm">{{ site('contact.phone') }} · {{ site('contact.emails') }}</p>

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
                    <option value="">Choose a slot…</option>
                    @forelse (
                        \App\Models\ConsultationSlot::withoutTenant()
                            ->where('is_available', true)
                            ->whereDate('date', '>=', now()->toDateString())
                            ->orderBy('date')
                            ->orderBy('start_time')
                            ->get() as $slot
                    )
                        <option value="{{ $slot->id }}">
                            {{ $slot->label() }}
                            @if ($slot->description) — {{ \Illuminate\Support\Str::limit($slot->description, 60) }} @endif
                        </option>
                    @empty
                        <option value="" disabled>No open slots right now</option>
                    @endforelse
                </select>
            </label>
            <label class="text-sm">Notes<textarea name="notes" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3"></textarea></label>
            <button class="rounded-full bg-[#16120f] px-6 py-3 text-white">{{ site('home.cta') }}</button>
        </form>
    @endif
</section>
@endsection
