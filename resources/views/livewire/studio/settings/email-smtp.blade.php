<div class="max-w-3xl">
    @include('livewire.studio.settings._nav')

    @if (session('status'))
        <p class="mb-6 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200">{{ session('status') }}</p>
    @endif

    <form wire:submit="save" class="space-y-6 rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/4">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Email / SMTP</h1>
            <p class="mt-2 text-sm text-[#16120f]/60">Outgoing mail for lead confirmations, booking emails, and payment receipts. Use <strong>Log</strong> on demo servers; switch to SMTP for production.</p>
        </div>

        <label class="block text-sm">
            <span class="font-medium">Mail mode</span>
            <select wire:model.live="mailer" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
                <option value="log">Log only (development)</option>
                <option value="smtp">SMTP</option>
            </select>
        </label>

        @if ($mailer === 'smtp')
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block text-sm sm:col-span-2">
                    <span class="opacity-70">SMTP host</span>
                    <input wire:model="host" type="text" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" placeholder="smtp.gmail.com">
                </label>
                <label class="block text-sm">
                    <span class="opacity-70">Port</span>
                    <input wire:model="port" type="number" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" placeholder="587">
                </label>
                <label class="block text-sm">
                    <span class="opacity-70">Encryption</span>
                    <select wire:model="encryption" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="none">None</option>
                    </select>
                </label>
                <label class="block text-sm sm:col-span-2">
                    <span class="opacity-70">Username</span>
                    <input wire:model="username" type="text" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" autocomplete="off">
                </label>
                <label class="block text-sm sm:col-span-2">
                    <span class="opacity-70">Password @if($password_set)<span class="text-emerald-700">(saved — leave blank to keep)</span>@endif</span>
                    <input wire:model="password" type="password" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" autocomplete="new-password">
                </label>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block text-sm">
                <span class="opacity-70">From address</span>
                <input wire:model="from_address" type="email" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" placeholder="booking@unikstudio.in">
            </label>
            <label class="block text-sm">
                <span class="opacity-70">From name</span>
                <input wire:model="from_name" type="text" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" placeholder="Unik Studio">
            </label>
        </div>

        <button type="submit" class="rounded-full bg-[#16120f] px-6 py-3 text-sm font-medium text-white">Save email settings</button>
    </form>
</div>
