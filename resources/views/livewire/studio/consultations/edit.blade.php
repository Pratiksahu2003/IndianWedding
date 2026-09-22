<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">{{ $isEdit ? 'Update' : 'Create' }}</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">{{ $isEdit ? 'Edit slot' : 'Open a slot' }}</h1>
        </div>
        <a href="{{ route('app.consultations.index') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to list</a>
    </div>

    <x-swal-flash />

    <form wire:submit="save" class="space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        <label class="block text-sm">
            <span class="font-medium text-[#16120f]/70">Title <span class="font-normal opacity-50">(optional)</span></span>
            <input wire:model="title" type="text" placeholder="e.g. Wedding discovery call" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
        </label>

        <div class="grid gap-3 sm:grid-cols-3">
            <label class="block text-sm sm:col-span-1">
                <span class="font-medium text-[#16120f]/70">Date *</span>
                <input wire:model="date" type="date" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            </label>
            <label class="block text-sm">
                <span class="font-medium text-[#16120f]/70">Start *</span>
                <input wire:model="start_time" type="time" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            </label>
            <label class="block text-sm">
                <span class="font-medium text-[#16120f]/70">End *</span>
                <input wire:model="end_time" type="time" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            </label>
        </div>

        <label class="block text-sm">
            <span class="font-medium text-[#16120f]/70">Description</span>
            <textarea wire:model="description" rows="4" placeholder="What this slot covers — package discussion, venue walkthrough, etc." class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm"></textarea>
        </label>

        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="is_available" class="rounded border-[#16120f]/20">
            Available for public booking
        </label>

        @error('date') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('start_time') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('end_time') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('description') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-5 py-2.5 text-sm text-white">{{ $isEdit ? 'Save changes' : 'Open slot' }}</button>
            <a href="{{ route('app.consultations.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10">Cancel</a>
        </div>
    </form>
</div>
