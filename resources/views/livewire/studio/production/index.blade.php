<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Production</h1>
        @if ($canCreate)
            <a href="{{ route('app.production.create') }}" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">New project</a>
        @endif
    </div>

    <x-swal-flash />

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($projects as $project)
            <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl">{{ $project->name }}</h2>
                        <p class="mt-1 text-sm opacity-70">{{ $project->images_count }} images · {{ $project->hasYoutubeVideo() ? 'YouTube linked' : 'No video' }}</p>
                        @if ($project->is_public)
                            <a class="mt-2 inline-block text-xs text-[#9b7b4b]" href="{{ $project->publicUrl() }}" target="_blank">Public page</a>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        @if ($canEdit)
                            <a href="{{ route('app.production.edit', $project) }}" class="text-xs text-[#9b7b4b]">Edit</a>
                        @endif
                        @if ($canDelete)
                            <button type="button" wire:click="delete({{ $project->id }})" wire:confirm="Remove this production project?" class="text-xs text-rose-700">Delete</button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl bg-white p-10 text-center md:col-span-2">
                <p>No production projects yet.</p>
                @if ($canCreate)
                    <a href="{{ route('app.production.create') }}" class="mt-4 inline-block rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create project</a>
                @endif
            </div>
        @endforelse
    </div>
</div>
