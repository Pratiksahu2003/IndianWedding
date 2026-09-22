<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Tasks</h1>
            <p class="mt-1 text-sm text-[#16120f]/50">Create and update on separate pages.</p>
        </div>
        @if ($canCreate)
        <a href="{{ route('app.tasks.create') }}" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Create task</a>
        @endif
    </div>

    <x-swal-flash />

    <div class="flex gap-4 overflow-x-auto">
        @foreach ($columns as $status => $tasks)
        <section class="w-72 shrink-0 rounded-3xl bg-white p-3">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">{{ str_replace('_', ' ', $status) }}</h2>
            <div class="mt-3 space-y-2">
                @forelse ($tasks as $task)
                <article class="rounded-2xl bg-[#f6f1ea] p-3 text-sm">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-medium">{{ $task->title }}</p>
                        <div class="flex shrink-0 gap-2">
                            @if ($canEdit)
                            <a href="{{ route('app.tasks.edit', $task) }}" class="text-[10px] text-[#9b7b4b]">Update</a>
                            @endif
                            @if ($canDelete)
                            <button type="button" wire:click="delete({{ $task->id }})" wire:confirm="Remove this task?" class="text-[10px] text-rose-700">Delete</button>
                            @endif
                        </div>
                    </div>
                    @if ($task->description)
                    <p class="mt-1 line-clamp-2 text-xs text-[#16120f]/55">{{ $task->description }}</p>
                    @endif
                    <p class="mt-1 text-xs opacity-50">{{ $task->project?->title }}</p>
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
