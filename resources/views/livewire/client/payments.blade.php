<div class="mx-auto max-w-4xl">
<h1 class="font-[Cormorant_Garamond] text-4xl">Payments</h1>
<div class="mt-8 space-y-4">
@foreach ($milestones as $m)
<div class="rounded-3xl bg-white/5 p-6"><p>{{ $m->name }}</p><p class="text-white/50">{{ \App\Support\Money::format($m->amount) }} · {{ $m->status->value }}</p></div>
@endforeach
@foreach ($invoices as $invoice)
<div class="rounded-3xl bg-white/5 p-6 flex justify-between"><span>{{ $invoice->invoice_number }}</span><a href="{{ route('invoices.pdf', $invoice) }}">Download</a></div>
@endforeach
</div>
</div>
