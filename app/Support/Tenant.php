<?php

namespace App\Support;

use App\Models\Organization;

class Tenant
{
    public static function id(): ?int
    {
        return app()->bound('current.organization_id')
            ? app('current.organization_id')
            : null;
    }

    public static function set(?int $id): void
    {
        app()->instance('current.organization_id', $id);
        StudioIntegrations::apply($id ? Organization::query()->find($id) : null);
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

    public static function run(?int $id, callable $callback): mixed
    {
        $previous = self::id();
        self::set($id);

        try {
            return $callback();
        } finally {
            self::set($previous);
        }
    }
}
