<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductionProject extends Model
{
    use BelongsToOrganization, HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'project_date' => 'date',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ProductionProject $project): void {
            $project->ulid ??= (string) Str::ulid();
            $project->slug ??= Str::slug($project->name);
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductionProjectImage::class)->orderBy('sort_order');
    }

    public function publicUrl(): string
    {
        return url('/production/'.$this->slug);
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

        return $this->images->first()?->url()
            ?? \App\Support\UnikStudioAssets::url('hero-slide.png');
    }
}
