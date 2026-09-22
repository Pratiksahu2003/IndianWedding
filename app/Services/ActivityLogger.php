<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Support\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public function log(string $action, ?Model $entity = null, array $metadata = []): ActivityLog
    {
        return ActivityLog::query()->create([
            'organization_id' => Tenant::id(),
            'actor_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entity ? $entity::class : null,
            'entity_id' => $entity?->getKey(),
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
