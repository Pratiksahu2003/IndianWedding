<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">Create</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">New production project</h1>
        </div>
        <a href="{{ route('app.production.index') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to list</a>
    </div>

    <x-swal-flash />

    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        <div class="grid gap-3 sm:grid-cols-2">
            <input wire:model="name" placeholder="Project name *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
            <input wire:model="subtitle" placeholder="Subtitle (optional)" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
            <select wire:model="category" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
                <option value="wedding-film">Wedding film</option>
                <option value="pre-wedding-film">Pre-wedding film</option>
                <option value="music-video">Music video</option>
                <option value="candid-documentary">Candid / documentary</option>
                <option value="birthday-event">Birthday / event</option>
                <option value="corporate-brand">Corporate / brand</option>
            </select>
            <input wire:model="client_name" placeholder="Client name" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="location" placeholder="Location" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <div class="sm:col-span-2">
                <x-studio.rich-textarea model="description" :rows="4" />
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium">YouTube video link</label>
                <input type="url" wire:model="youtube_url" placeholder="https://youtube.com/watch?v=…" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                @error('youtube_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <p class="mt-4 text-xs text-[#16120f]/50">You can upload multiple images on the next screen after saving.</p>
        <div class="mt-6 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-5 py-2.5 text-sm text-white">Create & add images</button>
            <a href="{{ route('app.production.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10">Cancel</a>
        </div>
    </form>
</div>
