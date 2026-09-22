<div class="space-y-6">
    <div class="rounded-3xl bg-white p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] opacity-50">{{ $project->project_number }}</p>
                <h1 class="font-[Cormorant_Garamond] text-4xl">{{ $project->title }}</h1>
                <p class="mt-2 text-sm opacity-70">{{ $project->customer?->name }} · {{ $project->venue }}{{ $project->city ? ', '.$project->city : '' }}</p>
                <p class="mt-1 text-sm opacity-50">Wedding {{ optional($project->wedding_date)?->toFormattedDateString() ?: 'TBC' }} · Booked {{ optional($project->booked_at)?->toFormattedDateString() }}</p>
            </div>
            <form wire:submit="updateStatus" class="flex gap-3">
                <select wire:model="status" class="rounded-2xl bg-[#f6f1ea] px-3 py-2 text-sm">
                    @foreach ($statuses as $status)<option value="{{ $status->value }}">{{ $status->label() }}</option>@endforeach
                </select>
                <button class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Save stage</button>
            </form>
        </div>
        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-[#f6f1ea] p-4"><p class="text-xs uppercase tracking-[0.2em] opacity-50">Total</p><p class="mt-1 text-xl">{{ \App\Support\Money::format($project->total_amount) }}</p></div>
            <div class="rounded-2xl bg-[#f6f1ea] p-4"><p class="text-xs uppercase tracking-[0.2em] opacity-50">Paid</p><p class="mt-1 text-xl">{{ \App\Support\Money::format($project->paidAmount()) }}</p></div>
            <div class="rounded-2xl bg-[#f6f1ea] p-4"><p class="text-xs uppercase tracking-[0.2em] opacity-50">Balance</p><p class="mt-1 text-xl">{{ \App\Support\Money::format($project->balance()) }}</p></div>
        </div>
        @if ($project->notes)
            <div class="cms-rich mt-6 text-sm leading-relaxed opacity-70">{!! rich_html($project->notes) !!}</div>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-3xl bg-white p-6">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Service / package</h2>
            @if ($project->package)
                <a href="{{ route('app.packages.show', $project->package) }}" class="mt-3 block font-[Cormorant_Garamond] text-2xl">{{ $project->package->name }}</a>
                <p class="mt-2 text-sm opacity-70">{{ $project->package->description }}</p>
            @else
                <p class="mt-3 text-sm opacity-50">No package attached.</p>
            @endif
        </section>
        <section class="rounded-3xl bg-white p-6">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Client</h2>
            <p class="mt-3 font-medium">{{ $project->customer?->name }}</p>
            <p class="text-sm opacity-70">{{ $project->customer?->email }} · {{ $project->customer?->phone }}</p>
        </section>
    </div>

    <section class="rounded-3xl bg-white p-6">
        <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Events</h2>
        <ul class="mt-4 space-y-3 text-sm">
            @forelse ($project->events as $event)
                <li class="rounded-2xl bg-[#f6f1ea] px-4 py-3">{{ $event->title }} · {{ optional($event->date)?->toFormattedDateString() }} {{ $event->start_time }}–{{ $event->end_time }} · {{ $event->venue ?: $project->venue }}</li>
            @empty
                <li class="opacity-50">No shoot events yet.</li>
            @endforelse
        </ul>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-3xl bg-white p-6">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Team</h2>
            <form wire:submit="assign" class="mt-3 flex gap-2">
                <select wire:model="assign_user_id" class="flex-1 rounded-2xl bg-[#f6f1ea] px-3 py-2 text-sm"><option value="">Choose</option>@foreach ($staff as $person)<option value="{{ $person->id }}">{{ $person->name }}</option>@endforeach</select>
                <select wire:model="assign_role" class="rounded-2xl bg-[#f6f1ea] px-3 py-2 text-sm">
                    <option value="photographer">Photographer</option><option value="videographer">Videographer</option><option value="editor">Editor</option>
                </select>
                <button class="rounded-full bg-[#16120f] px-3 py-2 text-xs text-white">Assign</button>
            </form>
            <ul class="mt-4 space-y-2 text-sm">@foreach ($project->team as $member)<li>{{ $member->user?->name }} · {{ $member->role->label() }}</li>@endforeach</ul>
        </section>
        <section class="rounded-3xl bg-white p-6">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Payments</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @foreach ($project->paymentMilestones as $m)
                    <li class="flex items-center justify-between rounded-2xl bg-[#f6f1ea] p-3">
                        <div>{{ $m->name }} · {{ $m->percentage }}% · {{ \App\Support\Money::format($m->amount) }}<div class="text-xs opacity-50">{{ $m->status->value }}</div></div>
                        @if ($m->remaining() > 0)<button wire:click="recordMilestonePayment({{ $m->id }})" class="text-xs">Record paid</button>@endif
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <section class="rounded-3xl bg-white p-6">
        <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Invoices</h2>
        <ul class="mt-4 space-y-2 text-sm">
            @forelse ($project->invoices as $invoice)
                <li class="flex items-center justify-between"><span>{{ $invoice->invoice_number }} · {{ $invoice->status->value }} · {{ \App\Support\Money::format($invoice->total) }}</span><a href="{{ route('invoices.pdf', $invoice) }}" class="text-[#9b7b4b]">PDF</a></li>
            @empty
                <li class="opacity-50">No invoices yet.</li>
            @endforelse
        </ul>
    </section>

    <section class="rounded-3xl bg-white p-6">
        <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Files</h2>
        <form wire:submit="uploadFile" class="mt-3 flex flex-wrap gap-2">
            <input type="file" wire:model="upload">
            <select wire:model="upload_kind" class="rounded-2xl bg-[#f6f1ea] px-3 py-2 text-sm">
                <option value="raw">RAW</option><option value="edited">Edited</option><option value="video">Video</option><option value="document">Document</option>
            </select>
            <button class="rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Upload</button>
            <div wire:loading wire:target="upload">Uploading…</div>
        </form>
        <ul class="mt-4 space-y-2 text-sm">@foreach ($project->files as $file)<li><a href="{{ route('files.show', $file) }}">{{ $file->original_name }}</a> · {{ $file->kind->value }}</li>@endforeach</ul>
        <button wire:click="releaseGallery" class="mt-4 rounded-full border px-4 py-2 text-sm">Release gallery</button>
        @if ($project->gallery)
            <p class="mt-2 text-xs opacity-50">Gallery {{ $project->gallery->is_released ? 'released' : 'draft' }}</p>
        @endif
    </section>

    <section class="rounded-3xl bg-white p-6">
        <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Tasks</h2>
        <form wire:submit="addTask" class="mt-3 flex gap-2"><input wire:model="task_title" class="flex-1 rounded-2xl bg-[#f6f1ea] px-3 py-2 text-sm"><button class="rounded-full bg-[#16120f] px-4 text-sm text-white">Add</button></form>
        <ul class="mt-3 text-sm">@foreach ($project->tasks as $task)<li>{{ $task->title }} · {{ $task->status->value }}</li>@endforeach</ul>
    </section>
</div>
