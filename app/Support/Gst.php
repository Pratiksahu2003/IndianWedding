<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\Organization;

class Gst
{
    /**
     * Common SAC for photography / videography services.
     */
    public const DEFAULT_SAC = '998386';

    public const DEFAULT_RATE = 18;

    /**
     * Rough state inference from city names used in the demo.
     */
    public static function stateFromCity(?string $city): ?string
    {
        if (! $city) {
            return null;
        }

        $city = strtolower(trim($city));

        $map = [
            'new delhi' => 'Delhi',
            'delhi' => 'Delhi',
            'udaipur' => 'Rajasthan',
            'jaipur' => 'Rajasthan',
            'mumbai' => 'Maharashtra',
            'pune' => 'Maharashtra',
            'bengaluru' => 'Karnataka',
            'bangalore' => 'Karnataka',
            'chennai' => 'Tamil Nadu',
            'hyderabad' => 'Telangana',
            'kolkata' => 'West Bengal',
            'ahmedabad' => 'Gujarat',
            'goa' => 'Goa',
        ];

        return $map[$city] ?? null;
    }

    public static function isInterstate(Organization $org, ?Customer $customer): bool
    {
        $seller = strtolower((string) ($org->state ?: ''));
        $buyerState = strtolower((string) (
            self::stateFromCity($customer?->city)
            ?? $customer?->city
            ?? ''
        ));

        if ($seller === '' || $buyerState === '') {
            return false;
        }

        return $seller !== $buyerState;
    }

    /**
     * Split tax amounts in minor units (paise).
     *
     * @return array{taxable:int,tax:int,cgst:int,sgst:int,igst:int,total:int}
     */
    public static function calculate(int $taxableMinor, int $rate = self::DEFAULT_RATE, bool $interstate = false): array
    {
        $tax = (int) round($taxableMinor * ($rate / 100));

        if ($interstate) {
            return [
                'taxable' => $taxableMinor,
                'tax' => $tax,
                'cgst' => 0,
                'sgst' => 0,
                'igst' => $tax,
                'total' => $taxableMinor + $tax,
            ];
        }

        $half = (int) round($tax / 2);

        return [
            'taxable' => $taxableMinor,
            'tax' => $half * 2,
            'cgst' => $half,
            'sgst' => $half,
            'igst' => 0,
            'total' => $taxableMinor + ($half * 2),
        ];
    }
}
