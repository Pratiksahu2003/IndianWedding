@php $isEdit = true; @endphp
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">{{ $isEdit ? 'Update' : 'Create' }}</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">{{ $isEdit ? 'Edit service' : 'New service' }}</h1>
        </div>
        <a href="{{ route('app.packages.index') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to list</a>
    </div>

    <x-swal-flash />

    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        <div class="grid gap-3 sm:grid-cols-2">
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
        </div>

        <div class="mt-6 space-y-4 border-t border-black/5 pt-6">
            <div>
                <h2 class="text-sm font-medium">Service images</h2>
                <p class="mt-1 text-xs text-[#16120f]/60">Upload one or more images. Each file can be up to 4 MB.</p>
            </div>

            @if ($package->images->isNotEmpty())
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($package->images as $image)
                        <div class="group relative overflow-hidden rounded-2xl bg-[#f6f1ea]">
                            <img src="{{ $image->url() }}" alt="{{ $package->name }}" class="h-36 w-full object-cover">
                            <button
                                type="button"
                                wire:click="removeImage({{ $image->id }})"
                                wire:confirm="Remove this image?"
                                class="absolute right-2 top-2 rounded-full bg-[#16120f]/80 px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100"
                            >
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="rounded-2xl bg-[#f6f1ea] p-4">
                <input
                    type="file"
                    wire:model="newImages"
                    accept="image/*"
                    multiple
                    class="block w-full text-sm file:mr-4 file:rounded-full file:border-0 file:bg-[#16120f] file:px-4 file:py-2 file:text-sm file:text-white"
                >
                <div wire:loading wire:target="newImages" class="mt-2 text-xs text-[#16120f]/60">Preparing files…</div>
                @if (! empty($newImages))
                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($newImages as $preview)
                            <img src="{{ $preview->temporaryUrl() }}" alt="Preview" class="h-28 w-full rounded-xl object-cover">
                        @endforeach
                    </div>
                    <button
                        type="button"
                        wire:click="uploadImages"
                        wire:loading.attr="disabled"
                        wire:target="uploadImages"
                        class="mt-4 rounded-full bg-[#16120f] px-4 py-2 text-sm text-white disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="uploadImages">Upload selected images</span>
                        <span wire:loading wire:target="uploadImages">Uploading…</span>
                    </button>
                @endif
            </div>
            @error('newImages') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
            @error('newImages.*') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        @error('name') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-6 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-5 py-2.5 text-sm text-white">{{ $isEdit ? 'Save changes' : 'Create service' }}</button>
            <a href="{{ route('app.packages.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10">Cancel</a>
        </div>
    </form>
</div>
