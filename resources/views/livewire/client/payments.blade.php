<div class="mx-auto max-w-4xl pt-8">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#c4a574]">Billing</p>
    <h1 class="mt-2 font-[Cormorant_Garamond] text-4xl">Payments</h1>
    <p class="mt-2 text-sm text-white/55">Milestones and invoices for your wedding booking.</p>

    @if ($milestones->isEmpty() && $invoices->isEmpty())
        <p class="mt-10 rounded-2xl border border-dashed border-white/15 px-6 py-10 text-center text-sm text-white/50">
            Payment details will appear here once your booking is confirmed.
        </p>
    @else
        <div class="mt-8 space-y-4">
            @foreach ($milestones as $m)
                <div class="rounded-3xl bg-white/5 p-6 ring-1 ring-white/10">
                    <p class="font-medium">{{ $m->name }}</p>
                    <p class="mt-1 text-sm text-white/50">
                        {{ \App\Support\Money::format($m->amount) }}
                        · <span class="capitalize">{{ str_replace('_', ' ', $m->status->value) }}</span>
                    </p>
                </div>
            @endforeach
            @foreach ($invoices as $invoice)
                <div class="flex items-center justify-between gap-4 rounded-3xl bg-white/5 p-6 ring-1 ring-white/10">
                    <span class="font-medium">{{ $invoice->invoice_number }}</span>
                    <a href="{{ route('invoices.pdf', $invoice) }}" class="shrink-0 rounded-full bg-[#c4a574] px-4 py-2 text-sm font-medium text-[#16120f]">Download PDF</a>
                </div>
            @endforeach
        </div>
    @endif
</div>
