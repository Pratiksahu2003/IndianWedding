<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Team</h1>
        <div class="flex flex-wrap gap-2">
            @if ($canManagePermissions)
            <a href="{{ route('app.team.permissions') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-2.5 text-sm font-medium shadow-sm ring-1 ring-black/5 hover:bg-[#f6f1ea]">
                Manage permissions
            </a>
            @endif
            @if ($canCreate)
            <button type="button" wire:click="create" class="inline-flex items-center gap-2 rounded-2xl bg-[#16120f] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#2a2218]">
                Add member
            </button>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-2xl bg-[#16120f] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
    @endif

    @if ($showForm && ($canCreate || $editingId))
    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="font-[Cormorant_Garamond] text-2xl">{{ $editingId ? 'Edit member' : 'Add team member' }}</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <input wire:model="name" placeholder="Full name *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="email" type="email" placeholder="Email *" @disabled($editingId) class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm disabled:opacity-60">
            <select wire:model="role" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                @foreach ($roles as $roleOption)
                <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                @endforeach
            </select>
        </div>
        @error('email') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">{{ $editingId ? 'Save changes' : 'Add member' }}</button>
            <button type="button" wire:click="cancel" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Cancel</button>
        </div>
    </form>
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($members as $member)
        <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-lg font-medium">{{ $member->user?->name }}</p>
                    <p class="text-sm text-[#16120f]/60">{{ $member->role->label() }}</p>
                    <p class="mt-1 text-xs text-[#16120f]/45">{{ $member->user?->email }}</p>
                </div>
                <div class="flex gap-2">
                    @if ($canEdit && ! $member->is_owner)
                    <button type="button" wire:click="edit({{ $member->id }})" class="text-xs text-[#9b7b4b]">Edit</button>
                    @endif
                    @if ($canDelete && ! $member->is_owner && $member->user_id !== auth()->id())
                    <button type="button" wire:click="delete({{ $member->id }})" wire:confirm="Remove this team member?" class="text-xs text-rose-700">Delete</button>
                    @endif
                </div>
            </div>
            <p class="mt-2 text-sm text-[#16120f]/50">{{ $workload[$member->user_id] ?? 0 }} active assignments</p>
            @if ($member->role === \App\Enums\Role::StudioAdmin)
            <p class="mt-3 inline-flex rounded-full bg-[#f6f1ea] px-2.5 py-1 text-[10px] font-semibold text-[#9b7b4b]">Full studio access</p>
            @elseif ($member->is_owner)
            <p class="mt-3 inline-flex rounded-full bg-[#f6f1ea] px-2.5 py-1 text-[10px] font-semibold text-[#9b7b4b]">Owner</p>
            @endif
        </article>
        @empty
        <p class="md:col-span-2">No team members yet.</p>
        @endforelse
    </div>
</div>
