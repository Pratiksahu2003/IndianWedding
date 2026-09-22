<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use App\Support\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder): void {
            $id = Tenant::id();
            if ($id) {
                $builder->where($builder->getModel()->getTable().'.organization_id', $id);
            }
        });

        static::creating(function (Model $model): void {
            if (! $model->getAttribute('organization_id') && Tenant::id()) {
                $model->setAttribute('organization_id', Tenant::id());
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeWithoutTenant(Builder $query): Builder
    {
        return $query->withoutGlobalScope('organization');
    }
}
