<?php

namespace App\Contracts;

use App\Models\Payment;

interface PaymentGateway
{
    public function name(): string;

    public function charge(Payment $payment, array $options = []): array;

    public function refund(Payment $payment, int $amount): array;

    public function verifyWebhook(string $payload, ?string $signature): bool;

    public function parseWebhook(array $payload): array;
}
