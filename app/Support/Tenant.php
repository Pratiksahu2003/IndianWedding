<?php

namespace App\Support;

use App\Models\Organization;

class Tenant
{
    public static function id(): ?int
    {
        if (app()->bound('current.organization_id')) {
            $bound = app('current.organization_id');
            if ($bound) {
                return (int) $bound;
            }
        }

        // Single-studio product: always fall back to the only organization.
        return self::soleOrganizationId();
    }

    public static function set(?int $id): void
    {
        $resolved = $id ?: self::soleOrganizationId();
        app()->instance('current.organization_id', $resolved);
        StudioIntegrations::apply($resolved ? Organization::query()->find($resolved) : null);
    }

    public static function forget(): void
    {
        app()->forgetInstance('current.organization_id');
    }

    public static function current(): ?Organization
    {
        $id = self::id();

        return $id ? Organization::query()->find($id) : null;
    }

    public static function requireId(): int
    {
        $id = self::id();
        abort_unless($id, 500, 'Studio context is missing.');

        return $id;
    }

    public static function soleOrganizationId(): ?int
    {
        static $cached = false;
        static $id = null;

        if ($cached === false) {
            $id = Organization::query()->orderBy('id')->value('id');
            $cached = true;
        }

        return $id ? (int) $id : null;
    }

    public static function run(?int $id, callable $callback): mixed
    {
        $previous = app()->bound('current.organization_id') ? app('current.organization_id') : null;
        self::set($id);

        try {
            return $callback();
        } finally {
            app()->instance('current.organization_id', $previous);
            StudioIntegrations::apply($previous ? Organization::query()->find($previous) : null);
        }
    }
}
