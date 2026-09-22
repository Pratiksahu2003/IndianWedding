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
    public function render()
    {
        $members = OrganizationUser::query()->with('user')->where('organization_id', Tenant::id())->get();
        $workload = ProjectTeamMember::query()->selectRaw('user_id, count(*) as total')->groupBy('user_id')->pluck('total', 'user_id');

        return view('livewire.studio.team.index', compact('members', 'workload'));
    }
}
