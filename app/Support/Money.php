<?php

namespace App\Support;

class Money
{
    public static function format(int $minor, string $currency = 'INR'): string
    {
        $amount = $minor / 100;
        $symbol = match ($currency) {
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $currency.' ',
        };

        return $symbol.number_format($amount, 2);
    }

    public static function fromMajor(float|int|string $major): int
    {
        return (int) round(((float) $major) * 100);
    }
}
