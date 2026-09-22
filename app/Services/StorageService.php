<?php

namespace App\Services;

use App\Contracts\CloudStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService implements CloudStorage
{
    public function disk(): string
    {
        return config('filesystems.media', config('filesystems.default', 'local'));
    }

    public function upload(UploadedFile|string $file, string $path, array $options = []): string
    {
        $disk = $this->disk();

        if ($file instanceof UploadedFile) {
            $name = $options['name'] ?? Str::ulid().'.'.$file->getClientOriginalExtension();
            $full = trim($path, '/').'/'.$name;
            Storage::disk($disk)->putFileAs($path, $file, $name, $options);

            return $full;
        }

        Storage::disk($disk)->put($path, $file, $options);

        return $path;
    }

    public function delete(string $path): void
    {
        Storage::disk($this->disk())->delete($path);
    }

    public function copy(string $from, string $to): void
    {
        Storage::disk($this->disk())->copy($from, $to);
    }

    public function move(string $from, string $to): void
    {
        Storage::disk($this->disk())->move($from, $to);
    }

    public function temporaryUrl(string $path, int $minutes = 30): string
    {
        $disk = Storage::disk($this->disk());

        if (method_exists($disk, 'temporaryUrl')) {
            try {
                return $disk->temporaryUrl($path, now()->addMinutes($minutes));
            } catch (\Throwable) {
                // Local disks do not support temporary URLs.
            }
        }

        return route('files.signed', ['path' => encrypt($path)]);
    }

    public function exists(string $path): bool
    {
        return Storage::disk($this->disk())->exists($path);
    }

    public function get(string $path): string
    {
        return Storage::disk($this->disk())->get($path);
    }
}
