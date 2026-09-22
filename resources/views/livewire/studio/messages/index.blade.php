<div class="max-w-2xl">
<h1 class="font-[Cormorant_Garamond] text-4xl">Messages</h1>
<div class="mt-6 space-y-3 rounded-3xl bg-white p-6">
@foreach ($messages as $message)
<p class="text-sm"><strong>{{ $message->sender?->name }}</strong> {{ $message->body }}</p>
@endforeach
</div>
<form wire:submit="send" class="mt-4 flex gap-2">
<input wire:model="body" class="flex-1 rounded-2xl bg-white px-4 py-3">
<button class="rounded-full bg-[#16120f] px-4 text-white">Send</button>
</form>
</div>
