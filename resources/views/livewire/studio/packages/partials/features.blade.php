<div class="sm:col-span-2">
    <h2 class="text-sm font-medium">Package features</h2>
    <p class="mt-1 text-xs text-[#16120f]/60">Add bullet points shown on the public page (coverage, deliverables, etc.).</p>
    <div class="mt-3 flex gap-2">
        <input wire:model="newFeature" wire:keydown.enter.prevent="addFeature" placeholder="Add a feature line" class="min-w-0 flex-1 rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
        <button type="button" wire:click="addFeature" class="shrink-0 rounded-full bg-[#16120f] px-4 py-2 text-sm text-white">Add</button>
    </div>
    @if ($featureItems !== [])
        <ul class="mt-4 space-y-2">
            @foreach ($featureItems as $index => $feature)
                <li class="flex items-start justify-between gap-3 rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                    <span>{{ $feature }}</span>
                    <button type="button" wire:click="removeFeature({{ $index }})" class="shrink-0 text-xs text-rose-700">Remove</button>
                </li>
            @endforeach
        </ul>
    @endif
</div>
