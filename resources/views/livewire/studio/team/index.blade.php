<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Team</h1>
<div class="mt-6 grid gap-4 md:grid-cols-2">
@forelse ($members as $member)
<article class="rounded-3xl bg-white p-6">
<p class="text-lg">{{ $member->user?->name }}</p>
<p class="text-sm opacity-60">{{ $member->role->label() }}</p>
<p class="mt-2 text-sm">{{ $workload[$member->user_id] ?? 0 }} active assignments</p>
</article>
@empty
<p>Invite your first teammate from settings.</p>
@endforelse
</div>
</div>
