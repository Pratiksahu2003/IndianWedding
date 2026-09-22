<form wire:submit="save" class="max-w-2xl space-y-4 rounded-3xl bg-white p-8">
<h1 class="font-[Cormorant_Garamond] text-4xl">Settings</h1>
<input wire:model="name" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="email" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="phone" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="timezone" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="currency" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="invoice_prefix" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="tax_id" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<textarea wire:model="milestones_json" rows="8" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 font-mono text-xs"></textarea>
<button class="rounded-full bg-[#16120f] px-5 py-3 text-white">Save</button>
</form>
