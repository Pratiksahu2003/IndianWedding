@props(['project'])

<a href="{{ $project->publicUrl() }}" {{ $attributes->merge(['class' => 'group flex h-full flex-col overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-black/5 transition hover:-translate-y-1 hover:shadow-xl']) }}>
    <div class="relative aspect-[4/3] overflow-hidden bg-[#f6f1ea]">
        <img
            src="{{ $project->coverUrl() }}"
            alt="{{ $project->name }}"
            class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
            loading="lazy"
        >
        @if ($project->hasYoutubeVideo())
            <span class="absolute bottom-3 right-3 rounded-full bg-[#16120f]/80 px-3 py-1 text-xs text-white">▶ Film</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-6">
        <p class="text-xs uppercase tracking-[0.16em] text-[#9b7b4b]">{{ str_replace('-', ' ', $project->category ?? 'Production') }}</p>
        <h3 class="mt-2 font-[Cormorant_Garamond] text-2xl group-hover:text-[#9b7b4b]">{{ $project->name }}</h3>
        @if ($project->subtitle)
            <p class="mt-2 line-clamp-2 text-sm text-[#16120f]/60">{{ $project->subtitle }}</p>
        @endif
        <p class="mt-auto pt-5 text-sm text-[#9b7b4b]">View project →</p>
    </div>
</a>
