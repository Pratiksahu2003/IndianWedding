<div class="max-w-3xl">
    @include('livewire.studio.settings._nav')

    <x-swal-flash />

    <form wire:submit="save" class="space-y-6 rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="font-[Cormorant_Garamond] text-4xl">Google Drive</h1>
                <p class="mt-2 text-sm text-[#16120f]/60">OAuth credentials for delivering client galleries and file transfers to your studio Drive. Secrets are stored encrypted for this studio only.</p>
            </div>
            @if ($configured)
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">Configured</span>
            @else
            <span class="rounded-full bg-[#f6f1ea] px-3 py-1 text-xs font-semibold text-[#9b7b4b]">Not connected</span>
            @endif
        </div>

        <section class="space-y-4 rounded-2xl bg-[#f6f1ea]/60 p-5">
            <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-[#16120f]/50">OAuth client</h2>
            <label class="block text-sm">
                <span class="opacity-70">Client ID</span>
                <input wire:model="client_id" type="text" class="mt-1 w-full rounded-2xl bg-white px-4 py-3" placeholder="xxxx.apps.googleusercontent.com" autocomplete="off">
            </label>
            <label class="block text-sm">
                <span class="opacity-70">Client secret @if($client_secret_set)<span class="text-emerald-700">(saved — leave blank to keep)</span>@endif</span>
                <input wire:model="client_secret" type="password" class="mt-1 w-full rounded-2xl bg-white px-4 py-3" autocomplete="new-password">
            </label>
            <label class="block text-sm">
                <span class="opacity-70">Refresh token @if($refresh_token_set)<span class="text-emerald-700">(saved — leave blank to keep)</span>@endif</span>
                <input wire:model="refresh_token" type="password" class="mt-1 w-full rounded-2xl bg-white px-4 py-3" autocomplete="new-password">
            </label>
        </section>

        <label class="block text-sm">
            <span class="font-medium">Default folder ID <span class="font-normal opacity-50">(optional)</span></span>
            <input wire:model="folder_id" type="text" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" placeholder="Google Drive folder ID for deliveries" autocomplete="off">
            <span class="mt-1 block text-xs text-[#16120f]/45">Open the folder in Drive and copy the ID from the URL after <code class="rounded bg-[#f6f1ea] px-1">/folders/</code>.</span>
        </label>

        <p class="text-xs leading-relaxed text-[#16120f]/50">
            Create an OAuth 2.0 Client ID in Google Cloud Console (Desktop or Web), enable the Google Drive API, then generate a refresh token with the <code class="rounded bg-[#f6f1ea] px-1">drive.file</code> scope.
        </p>

        <button type="submit" class="rounded-full bg-[#16120f] px-6 py-3 text-sm font-medium text-white">Save Google Drive settings</button>
    </form>
</div>
