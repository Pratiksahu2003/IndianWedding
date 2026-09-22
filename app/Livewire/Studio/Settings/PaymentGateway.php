<?php

namespace App\Livewire\Studio\Settings;

use App\Livewire\Studio\Settings\Concerns\ManagesStudioSettings;
use App\Support\StudioIntegrations;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Payment gateway')]
class PaymentGateway extends Component
{
    use ManagesStudioSettings;

    public string $default = 'manual';

    public string $stripe_key = '';

    public string $stripe_secret = '';

    public string $stripe_webhook_secret = '';

    public string $razorpay_key = '';

    public string $razorpay_secret = '';

    public string $razorpay_webhook_secret = '';

    public bool $stripe_secret_set = false;

    public bool $stripe_webhook_secret_set = false;

    public bool $razorpay_secret_set = false;

    public bool $razorpay_webhook_secret_set = false;

    public function mount(): void
    {
        $this->ensureSettingsAdmin();
        $data = StudioIntegrations::formValues('payments', [
            'default' => config('payments.default', 'manual'),
            'stripe_key' => '',
            'razorpay_key' => '',
        ]);
        $this->default = (string) ($data['default'] ?? 'manual');
        $this->stripe_key = (string) ($data['stripe_key'] ?? '');
        $this->razorpay_key = (string) ($data['razorpay_key'] ?? '');
        $this->stripe_secret_set = StudioIntegrations::secretIsSet('payments', 'stripe_secret');
        $this->stripe_webhook_secret_set = StudioIntegrations::secretIsSet('payments', 'stripe_webhook_secret');
        $this->razorpay_secret_set = StudioIntegrations::secretIsSet('payments', 'razorpay_secret');
        $this->razorpay_webhook_secret_set = StudioIntegrations::secretIsSet('payments', 'razorpay_webhook_secret');
    }

    public function save(): void
    {
        $this->ensureSettingsAdmin();
        $this->validate([
            'default' => 'required|in:manual,stripe,razorpay',
            'stripe_key' => 'nullable|string|max:255',
            'stripe_secret' => 'nullable|string|max:500',
            'stripe_webhook_secret' => 'nullable|string|max:500',
            'razorpay_key' => 'nullable|string|max:255',
            'razorpay_secret' => 'nullable|string|max:500',
            'razorpay_webhook_secret' => 'nullable|string|max:500',
        ]);

        $org = Tenant::current();
        $merged = StudioIntegrations::merge('payments', [
            'default' => $this->default,
            'stripe_key' => $this->stripe_key,
            'stripe_secret' => $this->stripe_secret,
            'stripe_webhook_secret' => $this->stripe_webhook_secret,
            'razorpay_key' => $this->razorpay_key,
            'razorpay_secret' => $this->razorpay_secret,
            'razorpay_webhook_secret' => $this->razorpay_webhook_secret,
        ], $org);

        $org->settings()->update(['payments' => $merged]);
        $org->load('settings');
        StudioIntegrations::apply($org);

        $this->stripe_secret = '';
        $this->stripe_webhook_secret = '';
        $this->razorpay_secret = '';
        $this->razorpay_webhook_secret = '';
        $this->stripe_secret_set = StudioIntegrations::secretIsSet('payments', 'stripe_secret');
        $this->stripe_webhook_secret_set = StudioIntegrations::secretIsSet('payments', 'stripe_webhook_secret');
        $this->razorpay_secret_set = StudioIntegrations::secretIsSet('payments', 'razorpay_secret');
        $this->razorpay_webhook_secret_set = StudioIntegrations::secretIsSet('payments', 'razorpay_webhook_secret');

        session()->flash('status', 'Payment gateway settings saved.');
    }

    public function render()
    {
        return view('livewire.studio.settings.payment-gateway', [
            'stripeWebhookUrl' => url('/webhooks/payments/stripe'),
            'razorpayWebhookUrl' => url('/webhooks/payments/razorpay'),
        ]);
    }
}
