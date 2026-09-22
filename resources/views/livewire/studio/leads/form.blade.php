<div class="max-w-2xl">
    <h1 class="font-[Cormorant_Garamond] text-4xl">New lead</h1>
    <form wire:submit="save" class="mt-8 grid gap-4 rounded-3xl bg-white p-8">
        <input wire:model="name" placeholder="Name" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <input wire:model="email" placeholder="Email" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <input wire:model="phone" placeholder="Phone" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <input wire:model="city" placeholder="City" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <input type="date" wire:model="wedding_date" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <input wire:model="venue" placeholder="Venue" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <input type="number" wire:model="budget" placeholder="Budget (INR major units)" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <select wire:model="package_id" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <option value="">Package</option>
            @foreach ($packages as $package)<option value="{{ $package->id }}">{{ $package->name }}</option>@endforeach
        </select>
        <x-studio.rich-textarea model="notes" placeholder="Notes" :rows="4" />
        @error('name') <p class="text-sm text-rose-700">{{ $message }}</p> @enderror
        <button class="rounded-full bg-[#16120f] py-3 text-white" wire:loading.attr="disabled">Save lead</button>
    </form>
</div>
