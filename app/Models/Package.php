<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Package extends Model
{
    use BelongsToOrganization, HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'includes_album' => 'boolean',
            'includes_video' => 'boolean',
            'includes_pre_wedding' => 'boolean',
            'includes_drone' => 'boolean',
            'additional_services' => 'array',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Package $package): void {
            $package->ulid ??= (string) Str::ulid();
            $package->slug ??= Str::slug($package->name);
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackageItem::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(PackageAddon::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function publicUrl(): string
    {
        return url('/services/'.$this->slug);
    }
}
