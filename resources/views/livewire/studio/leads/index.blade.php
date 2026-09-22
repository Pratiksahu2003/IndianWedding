<div>
    <x-swal-flash />
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Leads</h1>
        <div class="flex gap-2">
            <a href="{{ route('app.leads.pipeline') }}" class="rounded-full bg-white px-4 py-2 text-sm">Pipeline</a>
            <a href="{{ route('app.leads.create') }}" class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">New lead</a>
        </div>
    </div>
    <div class="mt-6 flex flex-wrap gap-3">
        <input wire:model.live.debounce.300ms="search" placeholder="Search" class="rounded-2xl bg-white px-4 py-2 text-sm">
        <select wire:model.live="status" class="rounded-2xl bg-white px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach ($statuses as $status)<option value="{{ $status->value }}">{{ $status->label() }}</option>@endforeach
        </select>
    </div>
    <div class="mt-6 overflow-hidden rounded-3xl bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#16120f]/5 text-xs uppercase tracking-wider"><tr><th class="px-4 py-3">Lead</th><th>Status</th><th>Date</th><th>Owner</th><th></th></tr></thead>
            <tbody>
            @forelse ($leads as $lead)
                <tr class="border-t border-[#16120f]/5">
                    <td class="px-4 py-3"><a href="{{ route('app.leads.show', $lead) }}" class="font-medium">{{ $lead->name }}</a><div class="text-xs opacity-50">{{ $lead->lead_number }}</div></td>
                    <td>{{ $lead->status->label() }}</td>
                    <td>{{ optional($lead->wedding_date)?->toFormattedDateString() }}</td>
                    <td>{{ $lead->assignee?->name }}</td>
                    <td><button wire:click="delete({{ $lead->id }})" wire:confirm="Archive this lead?" class="text-xs text-rose-700">Archive</button></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center">
                    <p class="font-[Cormorant_Garamond] text-2xl">No leads yet</p>
                    <p class="mt-2 text-sm opacity-60">Create a lead, import later, or send traffic to the website form.</p>
                    <a href="{{ route('app.leads.create') }}" class="mt-4 inline-block rounded-full bg-[#16120f] px-4 py-2 text-white">Create lead</a>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $leads->links() }}</div>
</div>
