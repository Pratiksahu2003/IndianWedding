<?php

namespace App\Events;

use App\Models\MediaFile;
use App\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FilesUploaded
{
    use Dispatchable, SerializesModels;

    public function __construct(public Project $project, public MediaFile $file) {}
}
