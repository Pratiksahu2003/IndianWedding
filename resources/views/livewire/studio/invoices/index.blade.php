<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Invoices</h1>
<div class="mt-6 overflow-hidden rounded-3xl bg-white">
<table class="w-full text-sm"><thead class="bg-[#16120f]/5 text-xs uppercase"><tr><th class="px-4 py-3">Invoice</th><th>Client</th><th>Total</th><th></th></tr></thead>
<tbody>@forelse ($invoices as $invoice)
<tr class="border-t"><td class="px-4 py-3">{{ $invoice->invoice_number }}</td><td>{{ $invoice->customer?->name }}</td><td>{{ \App\Support\Money::format($invoice->total) }}</td><td><a href="{{ route('invoices.pdf', $invoice) }}">PDF</a></td></tr>
@empty<tr><td class="px-4 py-10" colspan="4">No invoices yet.</td></tr>@endforelse</tbody></table>
</div>
<div class="mt-4">{{ $invoices->links() }}</div>
</div>
