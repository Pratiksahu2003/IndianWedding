<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Projects</h1>
            <p class="mt-1 text-sm text-[#16120f]/50">Create and update on separate pages.</p>
        </div>
        @if ($canCreate)
        <a href="{{ route('app.projects.create') }}" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create project</a>
        @endif
    </div>

    <x-swal-flash />

    <div class="flex flex-wrap gap-3">
        <input wire:model.live.debounce.300ms="search" class="rounded-2xl bg-white px-4 py-2 text-sm" placeholder="Search projects">
        <select wire:model.live="status" class="rounded-2xl bg-white px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach ($statuses as $st)
            <option value="{{ $st->value }}">{{ $st->label() }}</option>
            @endforeach
        </select>
    </div>

    <div class="overflow-hidden rounded-3xl bg-white">
        <table class="w-full text-sm">
            <thead class="bg-[#16120f]/5 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Project</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Wedding</th>
                    <th class="text-left">Client</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                <tr class="border-t border-[#16120f]/5">
                    <td class="px-4 py-3">
                        <a href="{{ route('app.projects.show', $project) }}" class="font-medium">{{ $project->title }}</a>
                        <div class="text-xs opacity-50">{{ $project->project_number }}</div>
                    </td>
                    <td>{{ $project->status->label() }}</td>
                    <td>{{ optional($project->wedding_date)?->toFormattedDateString() }}</td>
                    <td>{{ $project->customer?->name ?: '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        @if ($canEdit)
                        <a href="{{ route('app.projects.edit', $project) }}" class="text-xs text-[#9b7b4b]">Update</a>
                        @endif
                        @if ($canDelete)
                        <button type="button" wire:click="delete({{ $project->id }})" wire:confirm="Remove this project?" class="ml-2 text-xs text-rose-700">Delete</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center">
                        <p>No projects yet.</p>
                        @if ($canCreate)
                        <a href="{{ route('app.projects.create') }}" class="mt-4 inline-block rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create project</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $projects->links() }}</div>
</div>
