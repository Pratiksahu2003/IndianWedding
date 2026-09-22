<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Payments</h1>
        @if ($canCreate)
        <a href="{{ route('app.payments.create') }}" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Record payment</a>
        @endif
    </div>

    <x-swal-flash />

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($milestones as $m)
        <div class="rounded-3xl bg-white p-5 text-sm">
            <p>{{ $m->name }} · {{ $m->project?->title }}</p>
            <p class="opacity-60">{{ \App\Support\Money::format($m->remaining()) }} remaining · {{ $m->status->value }}</p>
        </div>
        @empty
        <p class="text-sm opacity-50">No open milestones.</p>
        @endforelse
    </div>

    <div class="overflow-hidden rounded-3xl bg-white">
        <table class="w-full text-sm">
            <thead class="bg-[#16120f]/5 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Reference</th>
                    <th class="text-left">Amount</th>
                    <th class="text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $p)
                <tr class="border-t border-[#16120f]/5">
                    <td class="px-4 py-3">{{ $p->reference }}</td>
                    <td>{{ \App\Support\Money::format($p->amount) }}</td>
                    <td>{{ $p->status->value }}</td>
                    <td class="px-4 py-3 text-right">
                        @if ($canDelete)
                        <button type="button" wire:click="delete({{ $p->id }})" wire:confirm="Remove this payment?" class="text-xs text-rose-700">Delete</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center">
                        <p>No payments recorded.</p>
                        @if ($canCreate)
                        <a href="{{ route('app.payments.create') }}" class="mt-4 inline-block rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Record payment</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $payments->links() }}</div>
</div>
