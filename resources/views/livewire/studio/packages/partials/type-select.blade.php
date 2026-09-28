<select wire:model="package_type" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
    @foreach (\App\Support\PackageOptions::types() as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>
