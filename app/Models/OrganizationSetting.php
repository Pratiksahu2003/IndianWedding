<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationSetting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'payment_milestones' => 'array',
            'whatsapp' => 'array',
            'email' => 'array',
            'storage' => 'array',
            'google_drive' => 'array',
            'notifications' => 'array',
            'seo' => 'array',
            'branding' => 'array',
            'feature_flags' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
