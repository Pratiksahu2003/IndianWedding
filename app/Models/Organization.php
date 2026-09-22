<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Organization $organization): void {
            $organization->ulid ??= (string) Str::ulid();
            $organization->slug ??= Str::slug($organization->name).'-'.Str::lower(Str::random(4));
        });

        static::created(function (Organization $organization): void {
            $organization->settings()->create([
                'payment_milestones' => [
                    ['name' => 'Booking Advance', 'percentage' => 50, 'due_condition' => 'booking'],
                    ['name' => 'Editing Phase', 'percentage' => 20, 'due_condition' => 'editing'],
                    ['name' => 'Final Payment', 'percentage' => 30, 'due_condition' => 'final_delivery'],
                ],
            ]);
        });
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_users')
            ->withPivot(['role', 'is_owner', 'permissions'])
            ->withTimestamps();
    }

    public function settings(): HasOne
    {
        return $this->hasOne(OrganizationSetting::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function defaultMilestones(): array
    {
        return $this->settings?->payment_milestones ?? [
            ['name' => 'Booking Advance', 'percentage' => 50, 'due_condition' => 'booking'],
            ['name' => 'Editing Phase', 'percentage' => 20, 'due_condition' => 'editing'],
            ['name' => 'Final Payment', 'percentage' => 30, 'due_condition' => 'final_delivery'],
        ];
    }
}
