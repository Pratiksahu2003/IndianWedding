<div class="max-w-3xl">
    @include('livewire.studio.settings._nav')

    @if (session('status'))
        <p class="mb-6 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200">{{ session('status') }}</p>
    @endif

    <form wire:submit="save" class="space-y-6 rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/4">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Payment gateway</h1>
            <p class="mt-2 text-sm text-[#16120f]/60">Choose how clients pay milestones — manual recording, Stripe, or Razorpay. Secrets are stored encrypted for your studio only.</p>
        </div>

        <label class="block text-sm">
            <span class="font-medium">Default gateway</span>
            <select wire:model="default" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3">
                <option value="manual">Manual (mark paid in CRM)</option>
                <option value="stripe">Stripe</option>
                <option value="razorpay">Razorpay</option>
            </select>
        </label>

        <section class="space-y-4 rounded-2xl bg-[#f6f1ea]/60 p-5">
            <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-[#16120f]/50">Stripe</h2>
            <label class="block text-sm">
                <span class="opacity-70">Publishable key</span>
                <input wire:model="stripe_key" type="text" class="mt-1 w-full rounded-2xl bg-white px-4 py-3" placeholder="pk_live_…" autocomplete="off">
            </label>
            <label class="block text-sm">
                <span class="opacity-70">Secret key @if($stripe_secret_set)<span class="text-emerald-700">(saved — leave blank to keep)</span>@endif</span>
                <input wire:model="stripe_secret" type="password" class="mt-1 w-full rounded-2xl bg-white px-4 py-3" placeholder="sk_live_…" autocomplete="new-password">
            </label>
            <label class="block text-sm">
                <span class="opacity-70">Webhook signing secret @if($stripe_webhook_secret_set)<span class="text-emerald-700">(saved)</span>@endif</span>
                <input wire:model="stripe_webhook_secret" type="password" class="mt-1 w-full rounded-2xl bg-white px-4 py-3" autocomplete="new-password">
            </label>
            <p class="text-xs text-[#16120f]/50">Webhook URL: <code class="rounded bg-white px-1.5 py-0.5">{{ $stripeWebhookUrl }}</code></p>
        </section>

        <section class="space-y-4 rounded-2xl bg-[#f6f1ea]/60 p-5">
            <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-[#16120f]/50">Razorpay</h2>
            <label class="block text-sm">
                <span class="opacity-70">Key ID</span>
                <input wire:model="razorpay_key" type="text" class="mt-1 w-full rounded-2xl bg-white px-4 py-3" autocomplete="off">
            </label>
            <label class="block text-sm">
                <span class="opacity-70">Key secret @if($razorpay_secret_set)<span class="text-emerald-700">(saved)</span>@endif</span>
                <input wire:model="razorpay_secret" type="password" class="mt-1 w-full rounded-2xl bg-white px-4 py-3" autocomplete="new-password">
            </label>
            <label class="block text-sm">
                <span class="opacity-70">Webhook secret @if($razorpay_webhook_secret_set)<span class="text-emerald-700">(saved)</span>@endif</span>
                <input wire:model="razorpay_webhook_secret" type="password" class="mt-1 w-full rounded-2xl bg-white px-4 py-3" autocomplete="new-password">
            </label>
            <p class="text-xs text-[#16120f]/50">Webhook URL: <code class="rounded bg-white px-1.5 py-0.5">{{ $razorpayWebhookUrl }}</code></p>
        </section>

        <button type="submit" class="rounded-full bg-[#16120f] px-6 py-3 text-sm font-medium text-white">Save payment settings</button>
    </form>
</div>
