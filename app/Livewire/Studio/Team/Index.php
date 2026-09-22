<?php

namespace App\Livewire\Studio\Team;

use App\Models\OrganizationUser;
use App\Models\ProjectTeamMember;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Team')]
class Index extends Component
{
    public function delete(int $id): void
    {
        $member = OrganizationUser::query()->findOrFail($id);
        $this->authorize('delete', $member);
        $member->delete();
        session()->flash('status', 'Team member removed.');
    }

    public function render()
    {
        $user = auth()->user();
        $members = OrganizationUser::query()->with('user')->where('organization_id', Tenant::id())->get();
        $workload = ProjectTeamMember::query()->selectRaw('user_id, count(*) as total')->groupBy('user_id')->pluck('total', 'user_id');

        return view('livewire.studio.team.index', [
            'members' => $members,
            'workload' => $workload,
            'canCreate' => $user->can('create', OrganizationUser::class),
            'canEdit' => $user->can('create', OrganizationUser::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', OrganizationUser::class),
            'canManagePermissions' => $user->canInOrganization('permissions.manage', Tenant::current()),
        ]);
    }
}
