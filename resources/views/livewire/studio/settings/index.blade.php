<div class="max-w-2xl">
    @include('livewire.studio.settings._nav')
    <x-swal-flash />
    <form wire:submit="save" class="space-y-4 rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/4">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Studio profile</h1>
        <p class="text-sm text-[#16120f]/50">These details appear on tax invoices (company info, GSTIN, address).</p>

        <label class="block text-xs font-medium text-[#16120f]/55">Studio name</label>
        <input wire:model="name" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" placeholder="Studio name">

        <label class="block text-xs font-medium text-[#16120f]/55">Legal / billing name</label>
        <input wire:model="legal_name" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" placeholder="Legal name on invoices">

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-[#16120f]/55">Email</label>
                <input wire:model="email" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            </div>
            <div>
                <label class="block text-xs font-medium text-[#16120f]/55">Phone</label>
                <input wire:model="phone" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            </div>
        </div>

        <label class="block text-xs font-medium text-[#16120f]/55">Address</label>
        <textarea wire:model="address" rows="2" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" placeholder="Full studio address"></textarea>

        <div class="grid gap-3 sm:grid-cols-3">
            <div>
                <label class="block text-xs font-medium text-[#16120f]/55">City</label>
                <input wire:model="city" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            </div>
            <div>
                <label class="block text-xs font-medium text-[#16120f]/55">State</label>
                <input wire:model="state" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            </div>
            <div>
                <label class="block text-xs font-medium text-[#16120f]/55">Country</label>
                <input wire:model="country" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-[#16120f]/55">GSTIN</label>
                <input wire:model="tax_id" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" placeholder="e.g. 07AABCU1234A1Z5">
            </div>
            <div>
                <label class="block text-xs font-medium text-[#16120f]/55">Invoice prefix</label>
                <input wire:model="invoice_prefix" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-[#16120f]/55">Timezone</label>
                <input wire:model="timezone" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            </div>
            <div>
                <label class="block text-xs font-medium text-[#16120f]/55">Currency</label>
                <input wire:model="currency" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
            </div>
        </div>

        <label class="block text-xs font-medium text-[#16120f]/55">Payment milestones (JSON)</label>
        <x-studio.rich-textarea model="milestones_json" :rows="8" plain class="font-mono text-xs" />

        <button class="rounded-full bg-[#16120f] px-5 py-3 text-white">Save</button>
    </form>
</div>
