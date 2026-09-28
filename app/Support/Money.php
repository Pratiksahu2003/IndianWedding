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

    public static function fileSize(int $bytes): string
    {
        if ($bytes >= 1_048_576) {
            return round($bytes / 1_048_576, 1).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}
