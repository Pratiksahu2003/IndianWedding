<?php

namespace App\Actions;

use App\Enums\FileKind;
use App\Events\FilesUploaded;
use App\Jobs\GenerateWatermarkedPreview;
use App\Models\MediaFile;
use App\Models\Project;
use App\Services\ActivityLogger;
use App\Services\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UploadProjectFile
{
    public function __construct(
        protected StorageService $storage,
        protected ActivityLogger $activity,
    ) {}

    public function handle(Project $project, UploadedFile $file, FileKind $kind, ?int $folderId = null): MediaFile
    {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'heic', 'mp4', 'mov', 'pdf', 'doc', 'docx'];
        $ext = strtolower($file->getClientOriginalExtension());

        if (! in_array($ext, $allowed, true)) {
            throw ValidationException::withMessages(['file' => 'This file type is not allowed.']);
        }

        if ($file->getSize() > 250 * 1024 * 1024) {
            throw ValidationException::withMessages(['file' => 'File exceeds the 250MB limit.']);
        }

        $path = $this->storage->upload(
            $file,
            sprintf('org/%s/projects/%s/%s', $project->organization_id, $project->id, $kind->value),
        );

        $media = MediaFile::query()->create([
            'organization_id' => $project->organization_id,
            'project_id' => $project->id,
            'folder_id' => $folderId,
            'uploaded_by' => Auth::id(),
            'name' => $file->getClientOriginalName(),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'disk' => $this->storage->disk(),
            'path' => $path,
            'kind' => $kind,
            'checksum' => hash_file('sha256', $file->getRealPath()) ?: null,
            'visibility' => 'private',
            'status' => 'ready',
        ]);

        $this->activity->log('file.uploaded', $media, ['kind' => $kind->value]);

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            GenerateWatermarkedPreview::dispatch($media->id);
        }

        event(new FilesUploaded($project, $media));

        return $media;
    }
}
