<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class WatermarkService
{
    public function __construct(protected StorageService $storage) {}

    public function generate(string $sourcePath, string $text = 'PREVIEW'): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        if (! $this->storage->exists($sourcePath)) {
            return null;
        }

        $binary = $this->storage->get($sourcePath);
        $image = @imagecreatefromstring($binary);
        if (! $image) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $color = imagecolorallocatealpha($image, 255, 255, 255, 70);
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($text);
        imagestring($image, $font, max(10, $width - $textWidth - 24), max(10, $height - 30), $text, $color);

        ob_start();
        imagejpeg($image, null, 82);
        $payload = ob_get_clean();
        imagedestroy($image);

        $preview = preg_replace('/(\.[a-z0-9]+)$/i', '-preview.jpg', $sourcePath) ?: $sourcePath.'-preview.jpg';
        Storage::disk($this->storage->disk())->put($preview, $payload);

        return $preview;
    }
}
