@php $isEdit = false; @endphp
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">{{ $isEdit ? 'Update' : 'Create' }}</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">{{ $isEdit ? 'Edit package' : 'New package' }}</h1>
        </div>
        <a href="{{ route('app.packages.index') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to list</a>
    </div>

    <x-swal-flash />

    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        <div class="grid gap-3 sm:grid-cols-2">
            <select wire:model="package_type" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
                <option value="service">Service (photography / cinematography)</option>
                <option value="wedding">Wedding package (Silver, Gold, etc.)</option>
                <option value="production">Production package (music video, films, etc.)</option>
                <option value="addon">Wedding add-on</option>
            </select>
            <input wire:model="name" placeholder="Name *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
            <input type="number" wire:model="price" placeholder="Price (₹)" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input type="number" wire:model="duration_hours" placeholder="Duration (hours)" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input type="number" wire:model="photographer_count" placeholder="Photographers" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input type="number" wire:model="videographer_count" placeholder="Videographers" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input type="number" wire:model="edited_photos" placeholder="Edited photos" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
            <div class="flex flex-wrap gap-4 text-sm sm:col-span-2">
                <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model="includes_album" class="rounded"> Album</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model="includes_video" class="rounded"> Video</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model="includes_pre_wedding" class="rounded"> Pre-wedding</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model="includes_drone" class="rounded"> Drone</label>
            </div>
            <div class="sm:col-span-2">
                <x-studio.rich-textarea model="description" :rows="5" />
            </div>
            @include('livewire.studio.packages.partials.features')
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[#16120f]/80">YouTube video link</label>
                <input
                    type="url"
                    wire:model="youtube_url"
                    placeholder="https://youtube.com/watch?v=… or https://youtu.be/…"
                    class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm"
                >
                <p class="mt-1.5 text-xs text-[#16120f]/50">Optional. Paste any normal YouTube URL — it will play embedded on the public service page.</p>
                @error('youtube_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
        @error('name') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-6 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-5 py-2.5 text-sm text-white">{{ $isEdit ? 'Save changes' : 'Create package' }}</button>
            <a href="{{ route('app.packages.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10">Cancel</a>
        </div>
    </form>
</div>
