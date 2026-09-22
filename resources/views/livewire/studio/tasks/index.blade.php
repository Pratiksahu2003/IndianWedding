<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Tasks</h1>
        @if ($canCreate)
        <button type="button" wire:click="create" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">New task</button>
        @endif
    </div>

    @if (session('status'))
        <div class="rounded-2xl bg-[#16120f] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
    @endif

    @if ($showForm && ($canCreate || $editingId))
    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="font-[Cormorant_Garamond] text-2xl">{{ $editingId ? 'Edit task' : 'New task' }}</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <input wire:model="title" placeholder="Task title *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
            <select wire:model="project_id" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                <option value="">Select project *</option>
                @foreach ($projects as $project)
                <option value="{{ $project->id }}">{{ $project->title }}</option>
                @endforeach
            </select>
            <select wire:model="assigned_to" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                <option value="">Unassigned</option>
                @foreach ($staff as $member)
                <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endforeach
            </select>
            <select wire:model="priority" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                @foreach (\App\Enums\TaskPriority::cases() as $priority)
                <option value="{{ $priority->value }}">{{ ucfirst($priority->value) }}</option>
                @endforeach
            </select>
        </div>
        @error('title') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('project_id') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">{{ $editingId ? 'Save changes' : 'Create task' }}</button>
            <button type="button" wire:click="cancel" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Cancel</button>
        </div>
    </form>
    @endif

    <div class="flex gap-4 overflow-x-auto">
        @foreach ($columns as $status => $tasks)
        <section class="w-72 shrink-0 rounded-3xl bg-white p-3">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">{{ str_replace('_', ' ', $status) }}</h2>
            <div class="mt-3 space-y-2">
                @forelse ($tasks as $task)
                <article class="rounded-2xl bg-[#f6f1ea] p-3 text-sm">
                    <div class="flex items-start justify-between gap-2">
                        <p>{{ $task->title }}</p>
                        <div class="flex shrink-0 gap-2">
                            @if ($canEdit)
                            <button type="button" wire:click="edit({{ $task->id }})" class="text-[10px] text-[#9b7b4b]">Edit</button>
                            @endif
                            @if ($canDelete)
                            <button type="button" wire:click="delete({{ $task->id }})" wire:confirm="Remove this task?" class="text-[10px] text-rose-700">Delete</button>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs opacity-50">{{ $task->project?->title }}</p>
                    @if ($task->assignee)
                    <p class="text-xs opacity-50">{{ $task->assignee->name }}</p>
                    @endif
                    <select class="mt-2 w-full rounded-xl bg-white text-xs" wire:change="move({{ $task->id }}, $event.target.value)">
                        @foreach (\App\Enums\TaskStatus::cases() as $st)
                        <option value="{{ $st->value }}" @selected($st === $task->status)>{{ str_replace('_', ' ', $st->value) }}</option>
                        @endforeach
                    </select>
                </article>
                @empty
                <p class="px-2 text-xs opacity-40">Empty</p>
                @endforelse
            </div>
        </section>
        @endforeach
    </div>
</div>
