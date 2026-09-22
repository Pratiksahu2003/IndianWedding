<div class="max-w-2xl">
@include('livewire.studio.settings._nav')
@if (session('status'))
    <p class="mb-6 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200">{{ session('status') }}</p>
@endif
<form wire:submit="save" class="space-y-4 rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/4">
<h1 class="font-[Cormorant_Garamond] text-4xl">Studio profile</h1>
<input wire:model="name" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="email" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="phone" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="timezone" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="currency" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="invoice_prefix" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<input wire:model="tax_id" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
<x-studio.rich-textarea model="milestones_json" :rows="8" plain class="font-mono text-xs" />
<button class="rounded-full bg-[#16120f] px-5 py-3 text-white">Save</button>
</form>
</div>
