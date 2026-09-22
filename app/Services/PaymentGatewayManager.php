<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Services\Payments\ManualGateway;
use App\Services\Payments\RazorpayGateway;
use App\Services\Payments\StripeGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function driver(?string $name = null): PaymentGateway
    {
        $name ??= config('payments.default', 'manual');

        return match ($name) {
            'manual' => app(ManualGateway::class),
            'stripe' => app(StripeGateway::class),
            'razorpay' => app(RazorpayGateway::class),
            default => throw new InvalidArgumentException("Unknown payment gateway [{$name}]."),
        };
    }
}
