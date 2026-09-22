<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface CloudStorage
{
    public function upload(UploadedFile|string $file, string $path, array $options = []): string;

    public function delete(string $path): void;

    public function temporaryUrl(string $path, int $minutes = 30): string;

    public function exists(string $path): bool;
}
