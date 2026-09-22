<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\Models\Payment;
use Illuminate\Support\Str;

class ManualGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'manual';
    }

    public function charge(Payment $payment, array $options = []): array
    {
        return [
            'id' => 'man_'.Str::lower(Str::random(12)),
            'status' => 'paid',
        ];
    }

    public function refund(Payment $payment, int $amount): array
    {
        return ['id' => 'ref_'.Str::lower(Str::random(12)), 'status' => 'refunded', 'amount' => $amount];
    }

    public function verifyWebhook(string $payload, ?string $signature): bool
    {
        return true;
    }

    public function parseWebhook(array $payload): array
    {
        return $payload;
    }
}
