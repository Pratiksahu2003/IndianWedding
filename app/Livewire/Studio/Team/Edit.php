<?php

namespace App\Livewire\Studio\Team;

use App\Enums\Role;
use App\Models\OrganizationUser;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Edit team member')]
class Edit extends Component
{
    public OrganizationUser $member;

    public string $name = '';

    public string $email = '';

    public string $role = 'photographer';

    public function mount(OrganizationUser $member): void
    {
        $this->authorize('update', $member);
        abort_if($member->is_owner, 403);

        $this->member = $member->load('user');
        $this->name = $member->user?->name ?? '';
        $this->email = $member->user?->email ?? '';
        $this->role = $member->role->value;
    }

    public function save()
    {
        $this->authorize('update', $this->member);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:120',
                Rule::unique('users', 'email')->ignore($this->member->user_id),
            ],
            'role' => ['required', Rule::enum(Role::class)],
        ]);

        $role = Role::from($data['role']);
        if ($role === Role::StudioAdmin || $role === Role::Client) {
            $this->addError('role', 'Choose a staff role for this member.');

            return;
        }

        $this->member->user?->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
        $this->member->update(['role' => $role]);

        session()->flash('status', 'Team member updated.');

        return $this->redirect(route('app.team.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.studio.team.edit', [
            'roles' => array_values(array_filter(
                Role::cases(),
                fn (Role $role) => $role->isStaff() && $role !== Role::StudioAdmin,
            )),
            'isEdit' => true,
        ]);
    }
}
