<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Gallery extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_released' => 'boolean',
            'allow_download' => 'boolean',
            'watermark_previews' => 'boolean',
            'released_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Gallery $gallery): void {
            $gallery->ulid ??= (string) Str::ulid();
            $gallery->access_key ??= Str::lower(Str::random(32));
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(GalleryAlbum::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class);
    }
}
