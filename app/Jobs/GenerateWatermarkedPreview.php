<?php

namespace App\Jobs;

use App\Models\MediaFile;
use App\Services\WatermarkService;
use App\Support\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateWatermarkedPreview implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $fileId) {}

    public function handle(WatermarkService $watermark): void
    {
        $file = MediaFile::withoutTenant()->find($this->fileId);
        if (! $file) {
            return;
        }

        Tenant::run($file->organization_id, function () use ($file, $watermark) {
            $path = $watermark->generate($file->path, $file->organization?->name ?: 'PREVIEW');
            if ($path) {
                $file->update(['preview_path' => $path]);
            }
        });
    }
}
