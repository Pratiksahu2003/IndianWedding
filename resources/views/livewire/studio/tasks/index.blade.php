<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Tasks</h1>
<div class="mt-6 flex gap-4 overflow-x-auto">
@foreach ($columns as $status => $tasks)
<section class="w-72 shrink-0 rounded-3xl bg-white p-3">
<h2 class="text-xs uppercase tracking-[0.2em] opacity-50">{{ str_replace('_',' ', $status) }}</h2>
<div class="mt-3 space-y-2">
@forelse ($tasks as $task)
<article class="rounded-2xl bg-[#f6f1ea] p-3 text-sm">
<p>{{ $task->title }}</p>
<p class="text-xs opacity-50">{{ $task->project?->title }}</p>
<select class="mt-2 w-full rounded-xl bg-white text-xs" wire:change="move({{ $task->id }}, $event.target.value)">
@foreach (\App\Enums\TaskStatus::cases() as $st)
<option value="{{ $st->value }}" @selected($st === $task->status)>{{ $st->value }}</option>
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
