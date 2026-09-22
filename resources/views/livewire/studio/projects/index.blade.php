<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Projects</h1>
<input wire:model.live.debounce.300ms="search" class="mt-6 rounded-2xl bg-white px-4 py-2 text-sm" placeholder="Search">
<div class="mt-6 overflow-hidden rounded-3xl bg-white">
<table class="w-full text-sm"><thead class="bg-[#16120f]/5 text-xs uppercase"><tr><th class="px-4 py-3">Project</th><th>Status</th><th>Wedding</th><th>Client</th></tr></thead>
<tbody>
@forelse ($projects as $project)
<tr class="border-t border-[#16120f]/5"><td class="px-4 py-3"><a href="{{ route('app.projects.show', $project) }}">{{ $project->title }}</a><div class="text-xs opacity-50">{{ $project->project_number }}</div></td><td>{{ $project->status->label() }}</td><td>{{ optional($project->wedding_date)?->toFormattedDateString() }}</td><td>{{ $project->customer?->name }}</td></tr>
@empty
<tr><td colspan="4" class="px-4 py-12 text-center">No projects yet. Convert a booked lead.</td></tr>
@endforelse
</tbody></table></div>
<div class="mt-4">{{ $projects->links() }}</div>
</div>
