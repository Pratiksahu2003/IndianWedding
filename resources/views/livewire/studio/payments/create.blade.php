<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">Create</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Record payment</h1>
        </div>
        <a href="{{ route('app.payments.index') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to list</a>
    </div>

    <x-swal-flash />

    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        <div class="grid gap-3 sm:grid-cols-2">
            <select wire:model="milestone_id" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
                <option value="">Select milestone *</option>
                @foreach ($openMilestones as $milestone)
                <option value="{{ $milestone->id }}">
                    {{ $milestone->name }} · {{ $milestone->project?->title }} · {{ \App\Support\Money::format($milestone->remaining()) }} due
                </option>
                @endforeach
            </select>
            <input wire:model="amount" type="number" step="0.01" min="0.01" placeholder="Amount (₹)" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
            <textarea wire:model="notes" placeholder="Notes" rows="3" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2"></textarea>
        </div>
        @error('milestone_id') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('amount') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-6 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-5 py-2.5 text-sm text-white">Save payment</button>
            <a href="{{ route('app.payments.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10">Cancel</a>
        </div>
    </form>
</div>
