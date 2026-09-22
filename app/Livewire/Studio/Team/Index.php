<?php

namespace App\Livewire\Studio\Team;

use App\Enums\Role;
use App\Models\OrganizationUser;
use App\Models\ProjectTeamMember;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Team')]
class Index extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $role = 'photographer';

    public function create(): void
    {
        $this->authorize('create', OrganizationUser::class);
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $member = OrganizationUser::query()->with('user')->findOrFail($id);
        $this->authorize('update', $member);

        $this->editingId = $member->id;
        $this->name = $member->user?->name ?? '';
        $this->email = $member->user?->email ?? '';
        $this->role = $member->role->value;
        $this->showForm = true;
    }

    public function save(): void
    {
        $ignoreUserId = null;
        if ($this->editingId) {
            $ignoreUserId = OrganizationUser::query()->find($this->editingId)?->user_id;
        }

        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:120',
                Rule::unique('users', 'email')->ignore($ignoreUserId),
            ],
            'role' => ['required', Rule::enum(Role::class)],
        ]);

        $role = Role::from($data['role']);
        if ($role === Role::StudioAdmin || $role === Role::Client) {
            $this->addError('role', 'Choose a staff role for this member.');

            return;
        }

        if ($this->editingId) {
            $member = OrganizationUser::query()->with('user')->findOrFail($this->editingId);
            $this->authorize('update', $member);

            $member->user?->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);
            $member->update(['role' => $role]);
            session()->flash('status', 'Team member updated.');
        } else {
            $this->authorize('create', OrganizationUser::class);

            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make(Str::password(16)),
                    'is_active' => true,
                ],
            );

            if ($user->wasRecentlyCreated === false) {
                $user->update(['name' => $data['name']]);
            }

            OrganizationUser::query()->updateOrCreate(
                [
                    'organization_id' => Tenant::requireId(),
                    'user_id' => $user->id,
                ],
                [
                    'role' => $role,
                    'invited_at' => now(),
                    'accepted_at' => now(),
                ],
            );

            session()->flash('status', 'Team member added.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $member = OrganizationUser::query()->findOrFail($id);
        $this->authorize('delete', $member);
        $member->delete();
        session()->flash('status', 'Team member removed.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset('showForm', 'editingId', 'name', 'email');
        $this->role = Role::Photographer->value;
    }

    public function render()
    {
        $user = auth()->user();
        $members = OrganizationUser::query()->with('user')->where('organization_id', Tenant::id())->get();
        $workload = ProjectTeamMember::query()->selectRaw('user_id, count(*) as total')->groupBy('user_id')->pluck('total', 'user_id');

        return view('livewire.studio.team.index', [
            'members' => $members,
            'workload' => $workload,
            'roles' => array_values(array_filter(
                Role::cases(),
                fn (Role $role) => $role->isStaff() && $role !== Role::StudioAdmin,
            )),
            'canCreate' => $user->can('create', OrganizationUser::class),
            'canEdit' => $user->can('create', OrganizationUser::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', OrganizationUser::class),
            'canManagePermissions' => $user->canInOrganization('permissions.manage', Tenant::current()),
        ]);
    }
}
