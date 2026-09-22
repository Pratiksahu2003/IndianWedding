<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean',
        ];
    }

    public function file()
    {
        return $this->belongsTo(MediaFile::class, 'file_id');
    }

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }
}
