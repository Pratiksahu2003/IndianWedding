<?php

namespace App\Livewire\Studio\Projects;

use App\Actions\AssignTeamMember;
use App\Actions\RecordPayment;
use App\Actions\UploadProjectFile;
use App\Enums\FileKind;
use App\Enums\ProjectStatus;
use App\Enums\Role;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.studio')]
class Show extends Component
{
    use WithFileUploads;

    public Project $project;
    public string $status;
    public ?int $assign_user_id = null;
    public string $assign_role = 'photographer';
    public $upload;
    public string $upload_kind = 'raw';
    public string $task_title = '';

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project->load(['customer', 'package', 'events', 'team.user', 'tasks.assignee', 'paymentMilestones', 'files', 'gallery', 'contract', 'invoices']);
        $this->status = $project->status->value;
    }

    public function updateStatus(): void
    {
        $this->authorize('update', $this->project);
        $this->project->update(['status' => ProjectStatus::from($this->status)]);
        $this->project->milestones()->where('key', $this->status)->update(['completed_at' => now()]);
        session()->flash('status', 'Project updated.');
    }

    public function assign(AssignTeamMember $action): void
    {
        $this->authorize('assign', $this->project);
        $user = User::query()->findOrFail($this->assign_user_id);
        $action->handle($this->project, $user, Role::from($this->assign_role));
        $this->project->load('team.user');
        session()->flash('status', 'Team member assigned.');
    }

    public function addTask(): void
    {
        $this->validate(['task_title' => ['required', 'string', 'max:180']]);
        ProjectTask::query()->create([
            'organization_id' => $this->project->organization_id,
            'project_id' => $this->project->id,
            'title' => $this->task_title,
            'status' => 'todo',
            'priority' => 'medium',
        ]);
        $this->task_title = '';
        $this->project->load('tasks.assignee');
    }

    public function uploadFile(UploadProjectFile $action): void
    {
        $this->validate(['upload' => ['required', 'file', 'max:256000']]);
        $action->handle($this->project, $this->upload, FileKind::from($this->upload_kind));
        $this->reset('upload');
        $this->project->load('files');
        session()->flash('status', 'File uploaded.');
    }

    public function recordMilestonePayment(int $milestoneId, RecordPayment $record): void
    {
        $milestone = $this->project->paymentMilestones()->findOrFail($milestoneId);
        $record->handle($milestone, $milestone->remaining());
        $this->project->load('paymentMilestones', 'payments');
        session()->flash('status', 'Payment recorded.');
    }

    public function releaseGallery(): void
    {
        $gallery = $this->project->gallery()->firstOrCreate([
            'organization_id' => $this->project->organization_id,
            'title' => $this->project->title.' Gallery',
        ], ['allow_download' => false, 'watermark_previews' => true]);
        $gallery->update(['is_released' => true, 'released_at' => now()]);
        event(new \App\Events\GalleryReleased($gallery));
        $this->project->load('gallery');
        session()->flash('status', 'Gallery released to client.');
    }

    public function render()
    {
        $this->project->refresh();

        return view('livewire.studio.projects.show', [
            'statuses' => ProjectStatus::cases(),
            'staff' => User::query()
                ->whereHas('memberships', fn ($q) => $q
                    ->where('organization_id', $this->project->organization_id)
                    ->whereIn('role', ['studio_admin', 'admin', 'manager', 'photographer', 'videographer', 'editor']))
                ->orderBy('name')
                ->get(),
            'teamMembers' => $this->project->team()->with('user')->get(),
            'milestones' => $this->project->paymentMilestones()->orderBy('sort_order')->get(),
            'projectFiles' => $this->project->files()->latest()->get(),
            'projectTasks' => $this->project->tasks()->with('assignee')->latest()->get(),
        ])->title($this->project->title);
    }
}
