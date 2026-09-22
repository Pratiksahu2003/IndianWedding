<?php

namespace App\Support;

use App\Models\Organization;
use App\Models\SiteContent;
use Illuminate\Support\Facades\Cache;

class SiteCopy
{
    public static function get(string $key, ?string $default = null): string
    {
        $value = self::all()[$key] ?? $default;

        return is_string($value) ? $value : (string) ($default ?? '');
    }

    public static function all(?int $organizationId = null): array
    {
        $organizationId ??= Tenant::id() ?? Organization::query()->where('is_active', true)->value('id');
        if (! $organizationId) {
            return [];
        }

        return Cache::remember('site.copy.'.$organizationId, 60, function () use ($organizationId) {
            return SiteContent::withoutTenant()
                ->where('organization_id', $organizationId)
                ->pluck('value', 'key')
                ->all();
        });
    }

    public static function forget(?int $organizationId = null): void
    {
        $organizationId ??= Tenant::id();
        if ($organizationId) {
            Cache::forget('site.copy.'.$organizationId);
        }
    }
}
