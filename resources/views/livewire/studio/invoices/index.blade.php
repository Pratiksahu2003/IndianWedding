<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Invoices</h1>
            <p class="mt-1 text-sm text-[#16120f]/50">GST invoices with HSN/SAC, company details and branded PDF.</p>
        </div>
        @if ($canCreate)
        <button type="button" wire:click="create" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">New invoice</button>
        @endif
    </div>

    <x-swal-flash />

    @if ($showForm && ($canCreate || $editingId))
    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="font-[Cormorant_Garamond] text-2xl">{{ $editingId ? 'Edit invoice' : 'New invoice' }}</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <select wire:model.live="customer_id" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                <option value="">Select client *</option>
                @foreach ($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}@if($customer->city) · {{ $customer->city }}@endif</option>
                @endforeach
            </select>
            <select wire:model="project_id" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                <option value="">No project linked</option>
                @foreach ($projects as $project)
                <option value="{{ $project->id }}">{{ $project->title }}</option>
                @endforeach
            </select>
            <select wire:model="status" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                @foreach ($statuses as $st)
                <option value="{{ $st->value }}">{{ str_replace('_', ' ', ucfirst($st->value)) }}</option>
                @endforeach
            </select>
            <input wire:model="issue_date" type="date" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="due_date" type="date" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="place_of_supply" placeholder="Place of supply (State)" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="billing_gstin" placeholder="Client GSTIN (optional)" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="hsn_sac" placeholder="HSN / SAC *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <select wire:model="gst_rate" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                <option value="0">GST 0%</option>
                <option value="5">GST 5%</option>
                <option value="12">GST 12%</option>
                <option value="18">GST 18%</option>
                <option value="28">GST 28%</option>
            </select>
            <input wire:model="quantity" type="number" min="1" placeholder="Qty" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="amount" type="number" step="0.01" min="0" placeholder="Unit amount (₹) *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="discount" type="number" step="0.01" min="0" placeholder="Discount (₹)" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="description" placeholder="Service / package description *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2 lg:col-span-3">
            <textarea wire:model="notes" placeholder="Notes / payment terms" rows="2" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2 lg:col-span-3"></textarea>
        </div>
        @if ($organization)
        <p class="mt-3 text-xs text-[#16120f]/45">
            Studio GSTIN: {{ $organization->tax_id ?: 'Add in Studio profile' }}
            · {{ $organization->city }}, {{ $organization->state }}
        </p>
        @endif
        @error('customer_id') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('amount') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('hsn_sac') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">{{ $editingId ? 'Save changes' : 'Create invoice' }}</button>
            <button type="button" wire:click="cancel" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Cancel</button>
        </div>
    </form>
    @endif

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
                        <button type="button" wire:click="edit({{ $invoice->id }})" class="ml-2 text-xs text-[#9b7b4b]">Edit</button>
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
                        <button type="button" wire:click="create" class="mt-4 rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create invoice</button>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $invoices->links() }}</div>
</div>
