<?php

namespace App\Livewire\Studio\Files;

use App\Actions\UploadProjectFile;
use App\Enums\FileKind;
use App\Models\MediaFile;
use App\Models\Project;
use App\Services\ActivityLogger;
use App\Support\Tenant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.studio')]
#[Title('Files')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url]
    public string $kind = '';

    #[Url]
    public string $search = '';

    public ?int $project_id = null;

    public string $upload_kind = 'edited';

    public $upload;

    public ?int $editingId = null;

    public string $edit_name = '';

    public string $edit_kind = 'document';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKind(): void
    {
        $this->resetPage();
    }

    public function uploadFile(UploadProjectFile $action): void
    {
        $this->authorize('create', MediaFile::class);

        $this->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'upload_kind' => ['required', Rule::enum(FileKind::class)],
            'upload' => ['required', 'file', 'max:256000'],
        ]);

        $project = Project::query()->findOrFail($this->project_id);
        $this->authorize('view', $project);

        $action->handle($project, $this->upload, FileKind::from($this->upload_kind));
        $this->reset('upload');
        session()->flash('status', 'File uploaded.');
    }

    public function startEdit(int $id): void
    {
        $file = MediaFile::query()->findOrFail($id);
        $this->authorize('update', $file);
        $this->editingId = $file->id;
        $this->edit_name = $file->original_name;
        $this->edit_kind = $file->kind->value;
    }

    public function cancelEdit(): void
    {
        $this->reset('editingId', 'edit_name', 'edit_kind');
    }

    public function updateFile(ActivityLogger $activity): void
    {
        $file = MediaFile::query()->findOrFail($this->editingId);
        $this->authorize('update', $file);

        $this->validate([
            'edit_name' => ['required', 'string', 'max:255'],
            'edit_kind' => ['required', Rule::enum(FileKind::class)],
        ]);

        $file->update([
            'name' => $this->edit_name,
            'original_name' => $this->edit_name,
            'kind' => FileKind::from($this->edit_kind),
        ]);

        $activity->log('file.updated', $file);
        $this->cancelEdit();
        session()->flash('status', 'File updated.');
    }

    public function delete(int $id, ActivityLogger $activity): void
    {
        $file = MediaFile::query()->findOrFail($id);
        $this->authorize('delete', $file);

        $disk = Storage::disk($file->disk ?: config('filesystems.media'));
        $disk->delete(array_values(array_filter([$file->path, $file->preview_path])));

        $activity->log('file.deleted', $file, ['name' => $file->original_name]);
        $file->delete();
        session()->flash('status', 'File removed.');
    }

    public function render()
    {
        $this->authorize('viewAny', MediaFile::class);

        $user = auth()->user();
        $org = Tenant::current();
        $managesAll = $user->canInOrganization('files.manage', $org)
            || $user->canInOrganization('files.raw', $org);

        return view('livewire.studio.files.index', [
            'files' => MediaFile::query()
                ->with(['project', 'uploader'])
                ->when(! $managesAll, fn ($q) => $q->whereHas(
                    'project.team',
                    fn ($team) => $team->where('user_id', $user->id),
                ))
                ->when($this->kind, fn ($q) => $q->where('kind', $this->kind))
                ->when($this->search, fn ($q) => $q->where('original_name', 'like', '%'.$this->search.'%'))
                ->latest()
                ->paginate(20),
            'projects' => Project::query()
                ->when(
                    ! $user->canInOrganization('files.manage', $org) && ! $user->canInOrganization('projects.manage', $org),
                    fn ($q) => $q->whereHas('team', fn ($team) => $team->where('user_id', $user->id)),
                )
                ->orderBy('title')
                ->get(['id', 'title']),
            'kinds' => FileKind::cases(),
            'canCreate' => $user->can('create', MediaFile::class),
        ]);
    }
}
