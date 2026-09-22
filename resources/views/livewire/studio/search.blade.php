<div x-data="{ open: false }" @keydown.window.slash.prevent="open=true; $refs.q.focus()" @keydown.window.ctrl.k.prevent="open=true; $refs.q.focus()">
<input x-ref="q" wire:model.live.debounce.200ms="q" @focus="open=true" placeholder="Search  /" class="w-64 rounded-full bg-white px-4 py-2 text-sm md:w-96">
<div x-show="open && '{{ $q }}'.length" x-cloak class="absolute mt-2 w-96 rounded-3xl bg-white p-4 text-sm shadow-xl">
@foreach (['leads','clients','projects','invoices','payments','files'] as $group)
    @if ($results[$group]->isNotEmpty())
        <p class="mt-2 text-xs uppercase opacity-40">{{ $group }}</p>
        @foreach ($results[$group] as $row)
            <p>{{ $row->name ?? $row->title ?? $row->invoice_number ?? $row->reference ?? $row->original_name }}</p>
        @endforeach
    @endif
@endforeach
</div>
</div>
