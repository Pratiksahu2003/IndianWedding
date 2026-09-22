<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">{{ $isEdit ? 'Update' : 'Create' }}</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">{{ $isEdit ? 'Edit invoice' : 'New invoice' }}</h1>
        </div>
        <a href="{{ route('app.invoices.index') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to list</a>
    </div>
    <x-swal-flash />
    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
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
        <div class="mt-6 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-5 py-2.5 text-sm text-white">{{ $isEdit ? 'Save changes' : 'Create invoice' }}</button>
            <a href="{{ route('app.invoices.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10">Cancel</a>
        </div>
    </form>
</div>