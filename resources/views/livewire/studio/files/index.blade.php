<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Files</h1>
<input wire:model.live="search" class="mt-4 rounded-2xl bg-white px-4 py-2 text-sm" placeholder="Search files">
<ul class="mt-6 divide-y rounded-3xl bg-white">
@forelse ($files as $file)
<li class="flex items-center justify-between px-5 py-4 text-sm"><span>{{ $file->original_name }}</span><a href="{{ route('files.show', $file) }}">Open</a></li>
@empty
<li class="px-5 py-10">No files uploaded.</li>
@endforelse
</ul>
<div class="mt-4">{{ $files->links() }}</div>
</div>
