<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">Create</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Create task</h1>
        </div>
        <a href="{{ route('app.tasks.index') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to board</a>
    </div>
    <x-swal-flash />
    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        <div class="grid gap-3 sm:grid-cols-2">
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
                @foreach (\App\Enums\TaskPriority::cases() as $priorityOption)
                <option value="{{ $priorityOption->value }}">{{ ucfirst($priorityOption->value) }}</option>
                @endforeach
            </select>
            <input wire:model="deadline" type="datetime-local" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <textarea wire:model="description" rows="4" placeholder="Description" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2"></textarea>
        </div>
        @error('title') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('project_id') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-6 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-5 py-2.5 text-sm text-white">Create task</button>
            <a href="{{ route('app.tasks.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10">Cancel</a>
        </div>
    </form>
</div>