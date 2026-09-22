<?php

namespace App\Http\Controllers;

use App\Models\FileAccessLog;
use App\Models\MediaFile;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileAccessController extends Controller
{
    public function signed(Request $request, StorageService $storage): StreamedResponse
    {
        $path = decrypt($request->query('path'));
        abort_unless($storage->exists($path), 404);

        return Storage::disk($storage->disk())->response($path);
    }

    public function show(Request $request, MediaFile $file, StorageService $storage): StreamedResponse
    {
        $this->authorize('view', $file);
        $preview = $request->boolean('preview') && $file->preview_path;

        FileAccessLog::query()->create([
            'organization_id' => $file->organization_id,
            'file_id' => $file->id,
            'user_id' => $request->user()?->id,
            'action' => $preview ? 'preview' : 'download',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $path = $preview ? $file->preview_path : $file->path;

        return Storage::disk($file->disk ?: $storage->disk())->download($path, $file->original_name);
    }
}
