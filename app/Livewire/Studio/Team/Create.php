<?php

namespace App\Livewire\Studio\Team;

use App\Enums\Role;
use App\Models\OrganizationUser;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Add team member')]
class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $role = 'photographer';

    public function save()
    {
        $this->authorize('create', OrganizationUser::class);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')],
            'role' => ['required', Rule::enum(Role::class)],
        ]);

        $role = Role::from($data['role']);
        if ($role === Role::StudioAdmin || $role === Role::Client) {
            $this->addError('role', 'Choose a staff role for this member.');

            return;
        }

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

        return $this->redirect(route('app.team.index'), navigate: true);
    }

    public function render()
    {
        $this->authorize('create', OrganizationUser::class);

        return view('livewire.studio.team.create', [
            'roles' => array_values(array_filter(
                Role::cases(),
                fn (Role $role) => $role->isStaff() && $role !== Role::StudioAdmin,
            )),
            'isEdit' => false,
        ]);
    }
}
