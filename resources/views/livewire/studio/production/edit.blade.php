<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">Update</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Edit production project</h1>
        </div>
        <a href="{{ route('app.production.index') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to list</a>
    </div>

    <x-swal-flash />

    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        <div class="grid gap-3 sm:grid-cols-2">
            <input wire:model="name" placeholder="Project name *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
            <input wire:model="subtitle" placeholder="Subtitle" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
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
            <input type="date" wire:model="project_date" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
            <div class="sm:col-span-2">
                <x-studio.rich-textarea model="description" :rows="4" />
            </div>
            <div class="sm:col-span-2">
                <x-studio.rich-textarea model="body" :rows="6" />
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium">YouTube video link</label>
                <input type="url" wire:model.live.debounce.500ms="youtube_url" placeholder="https://youtube.com/watch?v=…" class="w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                @error('youtube_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                @if ($youtube_url && \App\Support\YoutubeEmbed::embedUrl($youtube_url))
                    <div class="mt-4 max-w-xl">
                        <p class="mb-2 text-xs font-medium uppercase tracking-[0.14em] text-[#16120f]/40">Video preview</p>
                        <x-youtube-embed :url="$youtube_url" :title="$name" class="rounded-2xl" />
                    </div>
                @endif
            </div>
            <label class="flex items-center gap-2 text-sm sm:col-span-2">
                <input type="checkbox" wire:model="is_public" class="rounded"> Show on public Production page
            </label>
        </div>

        <div class="mt-8 space-y-4 border-t border-black/5 pt-6">
            <div>
                <h2 class="text-sm font-medium">Project images</h2>
                <p class="mt-1 text-xs text-[#16120f]/60">Upload multiple photos. Each file up to 4 MB.</p>
            </div>
            @if ($project->images->isNotEmpty())
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($project->images as $image)
                        <div class="group relative overflow-hidden rounded-2xl bg-[#f6f1ea]">
                            <img src="{{ $image->url() }}" alt="" class="h-36 w-full object-cover">
                            <button type="button" wire:click="removeImage({{ $image->id }})" wire:confirm="Remove this image?" class="absolute right-2 top-2 rounded-full bg-[#16120f]/80 px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100">Remove</button>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="rounded-2xl bg-[#f6f1ea] p-4">
                <input type="file" wire:model="newImages" accept="image/*" multiple class="block w-full text-sm file:mr-4 file:rounded-full file:border-0 file:bg-[#16120f] file:px-4 file:py-2 file:text-sm file:text-white">
                @if (! empty($newImages))
                    <button type="button" wire:click="uploadImages" class="mt-4 rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Upload selected images</button>
                @endif
            </div>
        </div>

        <div class="mt-6 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-5 py-2.5 text-sm text-white">Save project</button>
            @if ($project->is_public)
                <a href="{{ $project->publicUrl() }}" target="_blank" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10">View live</a>
            @endif
        </div>
    </form>
</div>
