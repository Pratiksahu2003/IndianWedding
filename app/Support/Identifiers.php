<?php

namespace App\Support;

use Illuminate\Support\Str;

class Identifiers
{
    public static function lead(int $organizationId): string
    {
        return sprintf('LD-%s-%s', str_pad((string) $organizationId, 3, '0', STR_PAD_LEFT), strtoupper(Str::random(6)));
    }

    public static function customer(int $organizationId): string
    {
        return sprintf('CL-%s-%s', str_pad((string) $organizationId, 3, '0', STR_PAD_LEFT), strtoupper(Str::random(6)));
    }

    public static function project(int $organizationId): string
    {
        return sprintf('PR-%s-%s', str_pad((string) $organizationId, 3, '0', STR_PAD_LEFT), strtoupper(Str::random(6)));
    }

    public static function invoice(string $prefix, int $next): string
    {
        return sprintf('%s-%s', strtoupper($prefix), str_pad((string) $next, 5, '0', STR_PAD_LEFT));
    }

    public static function payment(): string
    {
        return 'PAY-'.strtoupper((string) Str::ulid());
    }
}
