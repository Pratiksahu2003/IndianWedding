<?php

namespace App\Events;

use App\Models\Gallery;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GalleryReleased
{
    use Dispatchable, SerializesModels;

    public function __construct(public Gallery $gallery) {}
}
