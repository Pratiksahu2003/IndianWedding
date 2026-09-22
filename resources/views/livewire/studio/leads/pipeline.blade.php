<div>
    <h1 class="font-[Cormorant_Garamond] text-4xl">Pipeline</h1>
    <div class="mt-6 flex gap-4 overflow-x-auto pb-6">
        @foreach ($columns as $key => $column)
            <section class="w-72 shrink-0 rounded-3xl bg-white p-3">
                <h2 class="px-2 text-xs uppercase tracking-[0.2em] opacity-50">{{ $column['status']->label() }}</h2>
                <div class="mt-3 space-y-2">
                    @foreach ($column['leads'] as $lead)
                        <article class="rounded-2xl bg-[#f6f1ea] p-3">
                            <a href="{{ route('app.leads.show', $lead) }}" class="font-medium">{{ $lead->name }}</a>
                            <p class="text-xs opacity-60">{{ $lead->city }}</p>
                            <select class="mt-2 w-full rounded-xl bg-white text-xs" wire:change="move({{ $lead->id }}, $event.target.value)">
                                @foreach (\App\Enums\LeadStatus::pipeline() as $status)
                                    <option value="{{ $status->value }}" @selected($status === $lead->status)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</div>
