<div class="sm:col-span-2">
    <p class="mb-3 text-sm font-medium text-[#16120f]/80">Included in this package</p>
    <div class="grid gap-3 sm:grid-cols-2">
        @foreach (\App\Support\PackageOptions::includes() as $field => $label)
            <label class="inline-flex items-center gap-2 rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                <input type="checkbox" wire:model="{{ $field }}" class="rounded">
                {{ $label }}
            </label>
        @endforeach
    </div>
</div>
