<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Invoices</h1>
            <p class="mt-1 text-sm text-[#16120f]/50">GST invoices with HSN/SAC, company details and branded PDF.</p>
        </div>
        @if ($canCreate)
        <a href="{{ route('app.invoices.create') }}" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">New invoice</a>
        @endif
    </div>

    <x-swal-flash />

    <div class="overflow-hidden rounded-3xl bg-white">
        <table class="w-full text-sm">
            <thead class="bg-[#16120f]/5 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Invoice</th>
                    <th class="text-left">Client</th>
                    <th class="text-left">Project</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Total</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                <tr class="border-t border-[#16120f]/5">
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs opacity-50">{{ optional($invoice->issue_date)?->toFormattedDateString() }}</p>
                    </td>
                    <td>{{ $invoice->customer?->name ?: '—' }}</td>
                    <td class="text-xs opacity-70">{{ $invoice->project?->title ?: '—' }}</td>
                    <td>{{ str_replace('_', ' ', $invoice->status->value) }}</td>
                    <td>{{ \App\Support\Money::format($invoice->total) }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('invoices.pdf', $invoice) }}" class="text-xs text-[#9b7b4b]">PDF</a>
                        @if ($canEdit)
                        <a href="{{ route('app.invoices.edit', $invoice) }}" class="ml-2 text-xs text-[#9b7b4b]">Edit</a>
                        @endif
                        @if ($canDelete)
                        <button type="button" wire:click="delete({{ $invoice->id }})" wire:confirm="Remove this invoice?" class="ml-2 text-xs text-rose-700">Delete</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center">
                        <p>No invoices yet.</p>
                        @if ($canCreate)
                        <a href="{{ route('app.invoices.create') }}" class="mt-4 inline-block rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create invoice</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $invoices->links() }}</div>
</div>
