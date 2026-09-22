<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Payments</h1>
<div class="mt-6 grid gap-4 md:grid-cols-2">
@foreach ($milestones as $m)
<div class="rounded-3xl bg-white p-5 text-sm"><p>{{ $m->name }} · {{ $m->project?->title }}</p><p class="opacity-60">{{ \App\Support\Money::format($m->remaining()) }} remaining · {{ $m->status->value }}</p></div>
@endforeach
</div>
<div class="mt-6 overflow-hidden rounded-3xl bg-white">
<table class="w-full text-sm"><thead class="bg-[#16120f]/5 text-xs uppercase"><tr><th class="px-4 py-3">Reference</th><th>Amount</th><th>Status</th></tr></thead>
<tbody>@forelse ($payments as $p)<tr class="border-t"><td class="px-4 py-3">{{ $p->reference }}</td><td>{{ \App\Support\Money::format($p->amount) }}</td><td>{{ $p->status->value }}</td></tr>@empty<tr><td class="px-4 py-10" colspan="3">No payments recorded.</td></tr>@endforelse</tbody></table>
</div>
<div class="mt-4">{{ $payments->links() }}</div>
</div>
