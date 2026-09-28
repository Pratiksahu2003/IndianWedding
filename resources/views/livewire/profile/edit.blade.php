<div class="mx-auto max-w-3xl space-y-6 {{ auth()->user()?->isClient(\App\Support\Tenant::current()) ? 'pt-8' : '' }}">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] {{ auth()->user()?->isClient(\App\Support\Tenant::current()) ? 'text-[#c4a574]' : 'text-[#9b7b4b]' }}">Account</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl {{ auth()->user()?->isClient(\App\Support\Tenant::current()) ? 'text-white' : '' }}">My profile</h1>
            <p class="mt-1 text-sm opacity-60">Update your name, contact details, email, or password.</p>
        </div>
        <a href="{{ route($dashboardRoute) }}" class="rounded-full {{ auth()->user()?->isClient(\App\Support\Tenant::current()) ? 'bg-white/10 px-4 py-2 text-sm text-white ring-1 ring-white/15' : 'bg-white px-4 py-2 text-sm ring-1 ring-black/10' }}">Back to dashboard</a>
    </div>

    <x-swal-flash />

    <form wire:submit="save" class="space-y-6">
        <section class="rounded-3xl {{ auth()->user()?->isClient(\App\Support\Tenant::current()) ? 'bg-white/5 ring-1 ring-white/10' : 'bg-white shadow-sm ring-1 ring-black/5' }} p-6 sm:p-8">
            <h2 class="text-sm font-medium">Profile details</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <input wire:model="name" placeholder="Full name *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
                <input wire:model="email" type="email" placeholder="Email *" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm sm:col-span-2">
                <input wire:model="phone" type="tel" placeholder="Phone" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                <input wire:model="whatsapp" type="tel" placeholder="WhatsApp" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            </div>
            @error('name') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
            @error('email') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        </section>

        <section class="rounded-3xl {{ auth()->user()?->isClient(\App\Support\Tenant::current()) ? 'bg-white/5 ring-1 ring-white/10' : 'bg-white shadow-sm ring-1 ring-black/5' }} p-6 sm:p-8">
            <h2 class="text-sm font-medium">Change password</h2>
            <p class="mt-1 text-xs opacity-60">Leave blank to keep your current password. Use at least 10 characters.</p>
            <div class="mt-4 grid gap-3">
                <input wire:model="password" type="password" placeholder="New password" autocomplete="new-password" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                <input wire:model="password_confirmation" type="password" placeholder="Confirm new password" autocomplete="new-password" class="rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            </div>
            @error('password') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        </section>

        <section class="rounded-3xl {{ auth()->user()?->isClient(\App\Support\Tenant::current()) ? 'bg-white/5 ring-1 ring-white/10' : 'bg-white shadow-sm ring-1 ring-black/5' }} p-6 sm:p-8">
            <h2 class="text-sm font-medium">Confirm changes</h2>
            <p class="mt-1 text-xs opacity-60">Enter your current password when changing email or setting a new password.</p>
            <input wire:model="current_password" type="password" placeholder="Current password" autocomplete="current-password" class="mt-4 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
            @error('current_password') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
        </section>

        <button type="submit" class="rounded-full bg-[#16120f] px-6 py-3 text-sm font-medium text-white">Save profile</button>
    </form>
</div>
