<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Payments</h1>
        @if ($canCreate)
        <button type="button" wire:click="create" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Record payment</button>
        @endif
    </div>

    <x-swal-flash />
@if ($showForm && $canCreate)
    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="font-[Cormorant_Garamond] text-2xl">Record payment</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <select wire:model="milestone_id" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
                <option value="">Select milestone *</option>
                @foreach ($openMilestones as $milestone)
                <option value="{{ $milestone->id }}">
                    {{ $milestone->name }} · {{ $milestone->project?->title }} · {{ \App\Support\Money::format($milestone->remaining()) }} due
                </option>
                @endforeach
            </select>
            <input wire:model="amount" type="number" step="0.01" min="0.01" placeholder="Amount (₹)" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <textarea wire:model="notes" placeholder="Notes" rows="2" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2 lg:col-span-3"></textarea>
        </div>
        @error('milestone_id') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('amount') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Save payment</button>
            <button type="button" wire:click="cancel" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Cancel</button>
        </div>
    </form>
    @endif

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
                        <button type="button" wire:click="create" class="mt-4 rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Record payment</button>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $payments->links() }}</div>
</div>
