<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Files</h1>
            <p class="mt-1 text-sm text-[#16120f]/50">Upload, rename, and remove project files from one place.</p>
        </div>
    </div>

    <x-swal-flash />

    @if ($canCreate)
    <form wire:submit="uploadFile" class="rounded-3xl bg-white p-5">
        <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Upload file</h2>
        <div class="mt-3 flex flex-wrap items-end gap-3">
            <label class="min-w-[12rem] flex-1 text-xs">
                <span class="mb-1 block opacity-50">Project</span>
                <select wire:model="project_id" class="w-full rounded-2xl bg-[#f6f1ea] px-3 py-2 text-sm">
                    <option value="">Select project</option>
                    @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-xs">
                <span class="mb-1 block opacity-50">Kind</span>
                <select wire:model="upload_kind" class="rounded-2xl bg-[#f6f1ea] px-3 py-2 text-sm">
                    @foreach ($kinds as $fileKind)
                    <option value="{{ $fileKind->value }}">{{ str_replace('_', ' ', $fileKind->value) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-xs">
                <span class="mb-1 block opacity-50">File</span>
                <input type="file" wire:model="upload" class="block text-sm">
            </label>
            <button type="submit" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Upload</button>
            <p class="text-xs opacity-50" wire:loading wire:target="upload,uploadFile">Uploading…</p>
        </div>
        @if ($projects->isEmpty())
        <p class="mt-3 text-xs text-rose-700">Create a project first, then you can attach files here.</p>
        @endif
    </form>
    @endif

    <div class="flex flex-wrap gap-3">
        <input wire:model.live.debounce.300ms="search" class="rounded-2xl bg-white px-4 py-2 text-sm" placeholder="Search files">
        <select wire:model.live="kind" class="rounded-2xl bg-white px-3 py-2 text-sm">
            <option value="">All kinds</option>
            @foreach ($kinds as $fileKind)
            <option value="{{ $fileKind->value }}">{{ str_replace('_', ' ', $fileKind->value) }}</option>
            @endforeach
        </select>
    </div>

    <div class="overflow-hidden rounded-3xl bg-white">
        <table class="w-full text-sm">
            <thead class="bg-[#16120f]/5 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">File</th>
                    <th class="text-left">Project</th>
                    <th class="text-left">Kind</th>
                    <th class="text-left">Size</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($files as $file)
                <tr class="border-t border-[#16120f]/5" wire:key="file-{{ $file->id }}">
                    <td class="px-4 py-3">
                        @if ($editingId === $file->id)
                        <input wire:model="edit_name" class="w-full rounded-xl bg-[#f6f1ea] px-3 py-1.5 text-sm">
                        @else
                        <p class="font-medium">{{ $file->original_name }}</p>
                        <p class="text-xs opacity-50">{{ $file->uploader?->name ?: 'Unknown uploader' }}</p>
                        @endif
                    </td>
                    <td class="text-xs opacity-70">
                        @if ($file->project)
                        <a href="{{ route('app.projects.show', $file->project) }}" class="text-[#9b7b4b]">{{ $file->project->title }}</a>
                        @else
                        —
                        @endif
                    </td>
                    <td>
                        @if ($editingId === $file->id)
                        <select wire:model="edit_kind" class="rounded-xl bg-[#f6f1ea] px-2 py-1.5 text-xs">
                            @foreach ($kinds as $fileKind)
                            <option value="{{ $fileKind->value }}">{{ str_replace('_', ' ', $fileKind->value) }}</option>
                            @endforeach
                        </select>
                        @else
                        {{ str_replace('_', ' ', $file->kind->value) }}
                        @endif
                    </td>
                    <td class="text-xs opacity-70">{{ \App\Support\Money::fileSize($file->size) }}</td>
                    <td class="px-4 py-3 text-right">
                        @if ($editingId === $file->id)
                        <button type="button" wire:click="updateFile" class="text-xs text-[#9b7b4b]">Save</button>
                        <button type="button" wire:click="cancelEdit" class="ml-2 text-xs opacity-50">Cancel</button>
                        @else
                        <a href="{{ route('files.show', $file) }}" class="text-xs text-[#9b7b4b]">Open</a>
                        @can('update', $file)
                        <button type="button" wire:click="startEdit({{ $file->id }})" class="ml-2 text-xs text-[#9b7b4b]">Edit</button>
                        @endcan
                        @can('delete', $file)
                        <button type="button" wire:click="delete({{ $file->id }})" wire:confirm="Remove this file?" class="ml-2 text-xs text-rose-700">Delete</button>
                        @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center">
                        <p>No files uploaded.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $files->links() }}</div>
</div>
