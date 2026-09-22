<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\Models\Payment;
use RuntimeException;

class StripeGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'stripe';
    }

    public function charge(Payment $payment, array $options = []): array
    {
        if (! config('services.stripe.secret')) {
            throw new RuntimeException('Stripe is not configured.');
        }

        throw new RuntimeException('Stripe live charging requires STRIPE_SECRET.');
    }

    public function refund(Payment $payment, int $amount): array
    {
        throw new RuntimeException('Stripe is not configured.');
    }

    public function verifyWebhook(string $payload, ?string $signature): bool
    {
        $secret = config('services.stripe.webhook_secret');
        if (! $secret || ! $signature) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $payload, $secret), $signature);
    }

    public function parseWebhook(array $payload): array
    {
        return [
            'event_id' => $payload['id'] ?? null,
            'type' => $payload['type'] ?? null,
            'payment_id' => data_get($payload, 'data.object.metadata.payment_ulid'),
            'status' => data_get($payload, 'data.object.status'),
            'provider_payment_id' => data_get($payload, 'data.object.id'),
        ];
    }
}
