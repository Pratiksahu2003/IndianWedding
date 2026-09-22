@php
    $leadPopupDelayMs = 2500;
    $leadPopupHideHours = 24;
    $showLeadPopup = ! auth()->check() && ! session('inquiry_success');
@endphp

@if ($showLeadPopup)
<div
    x-data="leadPopup({ delay: {{ $leadPopupDelayMs }}, hideHours: {{ $leadPopupHideHours }} })"
    x-init="boot()"
    x-cloak
    class="pointer-events-none fixed inset-0 z-[60]"
    @keydown.escape.window="dismiss()"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="pointer-events-auto absolute inset-0 bg-[#0c0b0a]/55 backdrop-blur-[2px]"
        @click="dismiss()"
        aria-hidden="true"
    ></div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-250"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="pointer-events-auto absolute inset-x-0 bottom-0 mx-auto w-full max-w-lg md:bottom-8 md:px-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="lead-popup-title"
        @click.stop
    >
        <div class="relative overflow-hidden rounded-t-[28px] bg-[#f6f1ea] shadow-[0_-20px_60px_rgba(0,0,0,0.35)] md:rounded-[28px]">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#c4a574] via-[#e2c48a] to-[#c4a574]"></div>

            <button
                type="button"
                class="absolute right-3 top-3 z-10 inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#16120f]/8 text-[#16120f] transition hover:bg-[#16120f]/15"
                @click="dismiss()"
                aria-label="Close"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>

            <form method="POST" action="{{ route('inquiry.store') }}" class="px-5 pb-6 pt-7 sm:px-7 sm:pb-8 sm:pt-8" @submit="rememberDismiss()">
                @csrf
                <input type="hidden" name="source_hint" value="lead_popup">
                <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#9b7b4b]">{{ site('lead_popup.kicker', 'Quick enquiry') }}</p>
                <h2 id="lead-popup-title" class="mt-2 max-w-[90%] font-[Cormorant_Garamond] text-3xl leading-tight text-[#16120f] sm:text-4xl">
                    {{ site('lead_popup.heading', 'Tell us your date') }}
                </h2>
                <p class="mt-2 max-w-md text-sm leading-relaxed text-[#16120f]/65">
                    {{ site('lead_popup.body', 'Share a few details and our team will get back within one working day.') }}
                </p>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <label class="text-xs font-medium text-[#16120f]/70 sm:col-span-2">
                        Name
                        <input required name="name" autocomplete="name" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-white px-4 py-2.5 text-sm text-[#16120f] outline-none ring-[#c4a574]/40 focus:ring-2">
                    </label>
                    <label class="text-xs font-medium text-[#16120f]/70">
                        Phone
                        <input required name="phone" type="tel" autocomplete="tel" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-white px-4 py-2.5 text-sm text-[#16120f] outline-none ring-[#c4a574]/40 focus:ring-2">
                    </label>
                    <label class="text-xs font-medium text-[#16120f]/70">
                        Email
                        <input required name="email" type="email" autocomplete="email" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-white px-4 py-2.5 text-sm text-[#16120f] outline-none ring-[#c4a574]/40 focus:ring-2">
                    </label>
                    <label class="text-xs font-medium text-[#16120f]/70 sm:col-span-2">
                        Wedding date
                        <input name="wedding_date" type="date" class="mt-1 w-full rounded-2xl border border-[#16120f]/10 bg-white px-4 py-2.5 text-sm text-[#16120f] outline-none ring-[#c4a574]/40 focus:ring-2">
                    </label>
                    <label class="text-xs font-medium text-[#16120f]/70 sm:col-span-2">
                        Message
                        <textarea name="message" rows="2" placeholder="City, venue, or what you need…" class="mt-1 w-full resize-none rounded-2xl border border-[#16120f]/10 bg-white px-4 py-2.5 text-sm text-[#16120f] outline-none ring-[#c4a574]/40 focus:ring-2"></textarea>
                    </label>
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <button type="submit" class="rounded-full bg-[#16120f] px-6 py-2.5 text-sm font-medium text-white transition hover:bg-[#2a241f]">
                        {{ site('lead_popup.cta', 'Send enquiry') }}
                    </button>
                    <button type="button" class="text-sm text-[#16120f]/50 transition hover:text-[#16120f]" @click="dismiss()">
                        Not now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@elseif (session('inquiry_success'))
<script>
    try {
        localStorage.setItem('unik_lead_popup_hide_until', String(Date.now() + {{ $leadPopupHideHours }} * 60 * 60 * 1000));
    } catch (e) {}
</script>
@endif
