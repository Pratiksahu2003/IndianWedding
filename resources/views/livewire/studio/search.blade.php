<div class="relative w-full max-w-xl" x-data="{ open: false }" @keydown.window.slash.prevent="open=true; $refs.q.focus()" @keydown.window.ctrl.k.prevent="open=true; $refs.q.focus()">
    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#16120f]/35">
            <x-studio.icon name="search" class="h-4 w-4" />
        </span>
        <input
            x-ref="q"
            wire:model.live.debounce.200ms="q"
            @focus="open=true"
            placeholder="Search leads, clients, projects…"
            class="w-full rounded-2xl border border-[#16120f]/8 bg-white py-2.5 pl-10 pr-16 text-sm shadow-sm outline-none ring-[#c4a574]/30 placeholder:text-[#16120f]/35 focus:ring-4"
        >
        <kbd class="pointer-events-none absolute right-3 top-1/2 hidden -translate-y-1/2 rounded-md border border-[#16120f]/10 bg-[#f6f1ea] px-1.5 py-0.5 text-[10px] text-[#16120f]/45 sm:inline">⌘K</kbd>
    </div>
    <div x-show="open" x-cloak @click.outside="open=false" class="absolute z-40 mt-2 w-full overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5">
        @if (strlen(trim($q)) === 0)
            <p class="px-4 py-6 text-sm text-[#16120f]/45">Type to search across the studio. Press <span class="font-medium">/</span> anytime.</p>
        @else
            @php $any = collect($results)->flatten()->isNotEmpty(); @endphp
            @foreach (['leads' => 'Leads', 'clients' => 'Clients', 'projects' => 'Projects', 'invoices' => 'Invoices', 'payments' => 'Payments', 'files' => 'Files'] as $group => $label)
                @if ($results[$group]->isNotEmpty())
                    <p class="bg-[#f6f1ea] px-4 py-1.5 text-[10px] font-semibold uppercase tracking-[0.16em] text-[#16120f]/40">{{ $label }}</p>
                    @foreach ($results[$group] as $row)
                        @php
                            $href = match ($group) {
                                'leads' => route('app.leads.show', $row),
                                'projects' => route('app.projects.show', $row),
                                'invoices' => route('invoices.pdf', $row),
                                'files' => route('files.show', $row),
                                'clients' => route('app.clients.index'),
                                'payments' => route('app.payments.index'),
                                default => route('app.dashboard'),
                            };
                            $title = $row->name ?? $row->title ?? $row->invoice_number ?? $row->reference ?? $row->original_name;
                        @endphp
                        <a href="{{ $href }}" class="block px-4 py-2.5 text-sm hover:bg-[#f6f1ea]">{{ $title }}</a>
                    @endforeach
                @endif
            @endforeach
            @if (! $any)
                <p class="px-4 py-6 text-sm text-[#16120f]/45">No matches for “{{ $q }}”.</p>
            @endif
        @endif
    </div>
</div>
