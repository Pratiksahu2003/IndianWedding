@if (session('inquiry_success'))
    <div class="rounded-[28px] bg-[#16120f] p-10 text-white">
        <p class="font-[Cormorant_Garamond] text-4xl">We have your date.</p>
        <p class="mt-3 text-white/70">A producer will write within one working day. Your enquiry is already inside the studio CRM.</p>
    </div>
@else
<form method="POST" action="{{ route('inquiry.store') }}" class="rounded-[28px] bg-white p-8 shadow-sm">
    @csrf
    <h2 class="font-[Cormorant_Garamond] text-4xl">{{ site('reservation.heading', 'Begin with an enquiry') }}</h2>
    <p class="mt-2 text-sm text-[#16120f]/60">{{ site('reservation.body', 'This form creates a real studio lead.') }}</p>
    <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
    <div class="mt-8 grid gap-4 md:grid-cols-2">
        <label class="text-sm">Name<input required name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-[#f6f1ea] px-4 py-3"></label>
        <label class="text-sm">Email<input required type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-[#f6f1ea] px-4 py-3"></label>
        <label class="text-sm">Phone<input required name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-[#f6f1ea] px-4 py-3"></label>
        <label class="text-sm">WhatsApp<input name="whatsapp" value="{{ old('whatsapp') }}" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-[#f6f1ea] px-4 py-3"></label>
        <label class="text-sm">Wedding date<input type="date" name="wedding_date" value="{{ old('wedding_date') }}" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-[#f6f1ea] px-4 py-3"></label>
        <label class="text-sm">City<input name="city" value="{{ old('city') }}" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-[#f6f1ea] px-4 py-3"></label>
        <label class="text-sm">Venue<input name="venue" value="{{ old('venue') }}" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-[#f6f1ea] px-4 py-3"></label>
        <label class="text-sm">Guest count<input type="number" name="guest_count" value="{{ old('guest_count') }}" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-[#f6f1ea] px-4 py-3"></label>
        <label class="text-sm md:col-span-2">Budget (INR)<input type="number" name="budget" value="{{ old('budget') }}" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-[#f6f1ea] px-4 py-3"></label>
        <fieldset class="md:col-span-2 text-sm">
            <legend class="mb-2">Services</legend>
            <div class="flex flex-wrap gap-3">
                @foreach (['Photography','Cinematography','Pre-wedding','Drone','Album'] as $service)
                    <label class="rounded-full border border-[#16120f]/10 px-3 py-1"><input type="checkbox" name="services[]" value="{{ $service }}" class="mr-2">{{ $service }}</label>
                @endforeach
            </div>
        </fieldset>
        <label class="text-sm md:col-span-2">Message<textarea name="message" rows="4" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-[#f6f1ea] px-4 py-3">{{ old('message') }}</textarea></label>
    </div>
    @if ($errors->any())
        <p class="mt-4 text-sm text-rose-700">{{ $errors->first() }}</p>
    @endif
    <button class="mt-6 rounded-full bg-[#16120f] px-6 py-3 text-white">Send enquiry</button>
</form>
@endif
