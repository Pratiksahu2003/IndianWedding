<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">{{ $isEdit ? 'Update' : 'Create' }}</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">{{ $isEdit ? 'Edit member' : 'Add team member' }}</h1>
        </div>
        <a href="{{ route('app.team.index') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to team</a>
    </div>
    <x-swal-flash />
    <form wire:submit="save" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        <div class="grid gap-3 sm:grid-cols-2">
            <input wire:model="name" placeholder="Full name *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            <input wire:model="email" type="email" placeholder="Email *" @disabled($isEdit) class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm disabled:opacity-60">
            <select wire:model="role" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
                @foreach ($roles as $roleOption)
                <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                @endforeach
            </select>
        </div>
        @error('email') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('role') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        <div class="mt-6 flex gap-2">
            <button type="submit" class="rounded-full bg-[#16120f] px-5 py-2.5 text-sm text-white">{{ $isEdit ? 'Save changes' : 'Add member' }}</button>
            <a href="{{ route('app.team.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10">Cancel</a>
        </div>
    </form>
</div>
