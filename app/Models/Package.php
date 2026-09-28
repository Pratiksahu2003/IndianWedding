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
            'includes_candid' => 'boolean',
            'includes_instagram_reels' => 'boolean',
            'includes_same_day_edit' => 'boolean',
            'includes_live_streaming' => 'boolean',
            'includes_invitation_video' => 'boolean',
            'includes_destination' => 'boolean',
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

    public function images(): HasMany
    {
        return $this->hasMany(PackageImage::class)->orderBy('sort_order');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function publicUrl(): string
    {
        return match (\App\Support\PackageOptions::listingPageForType($this->package_type ?? 'service')) {
            'packages' => url('/packages/'.$this->slug),
            'production' => url('/services/'.$this->slug),
            default => url('/services/'.$this->slug),
        };
    }

    public function typeLabel(): string
    {
        return \App\Support\PackageOptions::typeLabel($this->package_type ?? 'service');
    }

    /** @return list<string> */
    public function activeIncludes(): array
    {
        $active = [];

        foreach (\App\Support\PackageOptions::includes() as $key => $label) {
            if ($this->{$key}) {
                $active[] = $label;
            }
        }

        return $active;
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('package_type', $type);
    }

    public function youtubeEmbedUrl(): ?string
    {
        return \App\Support\YoutubeEmbed::embedUrl($this->youtube_url);
    }

    public function hasYoutubeVideo(): bool
    {
        return $this->youtubeEmbedUrl() !== null;
    }

    public function coverUrl(): string
    {
        if ($this->cover_image) {
            return $this->cover_image;
        }

        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            return $this->images->first()->url();
        }

        $covers = \App\Support\UnikStudioAssets::serviceCovers();

        if (isset($covers[$this->slug])) {
            return \App\Support\UnikStudioAssets::url($covers[$this->slug]);
        }

        $context = match ($this->package_type) {
            'wedding', 'destination' => 'wedding',
            'production', 'music_video' => 'production',
            default => null,
        };

        return \App\Support\UnikStudioAssets::placeholderCover($this->id ?? $this->slug, $context);
    }
}
