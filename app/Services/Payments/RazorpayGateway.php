<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\Models\Payment;
use RuntimeException;

class RazorpayGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'razorpay';
    }

    public function charge(Payment $payment, array $options = []): array
    {
        if (! config('services.razorpay.key')) {
            throw new RuntimeException('Razorpay is not configured.');
        }

        throw new RuntimeException('Razorpay live charging requires RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET.');
    }

    public function refund(Payment $payment, int $amount): array
    {
        throw new RuntimeException('Razorpay is not configured.');
    }

    public function verifyWebhook(string $payload, ?string $signature): bool
    {
        $secret = config('services.razorpay.webhook_secret');
        if (! $secret || ! $signature) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $payload, $secret), $signature);
    }

    public function parseWebhook(array $payload): array
    {
        return [
            'event_id' => $payload['event'] ?? ($payload['id'] ?? null),
            'type' => $payload['event'] ?? null,
            'payment_id' => data_get($payload, 'payload.payment.entity.notes.payment_ulid'),
            'status' => data_get($payload, 'payload.payment.entity.status'),
            'provider_payment_id' => data_get($payload, 'payload.payment.entity.id'),
        ];
    }
}
