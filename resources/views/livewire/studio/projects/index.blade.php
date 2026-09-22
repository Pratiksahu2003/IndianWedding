<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Projects</h1>
            <p class="mt-1 text-sm text-[#16120f]/50">Create and update projects in separate forms.</p>
        </div>
        @if ($canCreate)
        <button type="button" wire:click="openCreate" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create project</button>
        @endif
    </div>

    <x-swal-flash />

    {{-- CREATE FORM --}}
    @if ($formMode === 'create' && $canCreate)
    <form wire:submit="store" wire:key="project-create-form" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-[Cormorant_Garamond] text-2xl">Create project</h2>
            <span class="rounded-full bg-[#dde8e4] px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-[#2e5e52]">New</span>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <input wire:model="title" placeholder="Project title *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
            <select wire:model="customer_id" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                <option value="">Select client *</option>
                @foreach ($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            <input wire:model="wedding_date" type="date" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="venue" placeholder="Venue" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="city" placeholder="City" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <select wire:model="statusForm" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                @foreach ($statuses as $st)
                <option value="{{ $st->value }}">{{ $st->label() }}</option>
                @endforeach
            </select>
            <textarea wire:model="description" rows="4" placeholder="Description / notes" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2 lg:col-span-3"></textarea>
        </div>
        @error('title') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('customer_id') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create project</button>
            <button type="button" wire:click="cancel" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Cancel</button>
        </div>
    </form>
    @endif

    {{-- UPDATE FORM --}}
    @if ($formMode === 'edit' && $canEdit && $editingId)
    <form wire:submit="update" wire:key="project-edit-form-{{ $editingId }}" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-[Cormorant_Garamond] text-2xl">Update project</h2>
            <span class="rounded-full bg-[#e8ddd4] px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-[#7a5c38]">Edit</span>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <input wire:model="title" placeholder="Project title *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
            <select wire:model="customer_id" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                <option value="">Select client *</option>
                @foreach ($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            <input wire:model="wedding_date" type="date" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="venue" placeholder="Venue" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="city" placeholder="City" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <select wire:model="statusForm" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                @foreach ($statuses as $st)
                <option value="{{ $st->value }}">{{ $st->label() }}</option>
                @endforeach
            </select>
            <textarea wire:model="description" rows="4" placeholder="Description / notes" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2 lg:col-span-3"></textarea>
        </div>
        @error('title') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('customer_id') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Update project</button>
            <button type="button" wire:click="cancel" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Cancel</button>
        </div>
    </form>
    @endif

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
                        @if ($project->notes)
                        <div class="mt-1 line-clamp-1 text-xs text-[#16120f]/45">{{ $project->notes }}</div>
                        @endif
                    </td>
                    <td>{{ $project->status->label() }}</td>
                    <td>{{ optional($project->wedding_date)?->toFormattedDateString() }}</td>
                    <td>{{ $project->customer?->name ?: '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        @if ($canEdit)
                        <button type="button" wire:click="openEdit({{ $project->id }})" class="text-xs text-[#9b7b4b]">Update</button>
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
                        <button type="button" wire:click="openCreate" class="mt-4 rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create project</button>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $projects->links() }}</div>
</div>
