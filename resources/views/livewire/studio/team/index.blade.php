<div>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Team</h1>
        @if (auth()->user()?->canInOrganization('permissions.manage', \App\Support\Tenant::current()))
        <a href="{{ route('app.team.permissions') }}" class="inline-flex items-center gap-2 rounded-2xl bg-[#16120f] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#2a2218]">
            Manage permissions
        </a>
        @endif
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        @forelse ($members as $member)
        <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
            <p class="text-lg font-medium">{{ $member->user?->name }}</p>
            <p class="text-sm text-[#16120f]/60">{{ $member->role->label() }}</p>
            <p class="mt-2 text-sm text-[#16120f]/50">{{ $workload[$member->user_id] ?? 0 }} active assignments</p>
            @if ($member->role === \App\Enums\Role::StudioAdmin)
            <p class="mt-3 inline-flex rounded-full bg-[#f6f1ea] px-2.5 py-1 text-[10px] font-semibold text-[#9b7b4b]">Full studio access</p>
            @endif
        </article>
        @empty
        <p>Invite your first teammate from settings.</p>
        @endforelse
    </div>
</div>
