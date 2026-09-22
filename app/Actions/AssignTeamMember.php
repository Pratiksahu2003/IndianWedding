<?php

namespace App\Actions;

use App\Enums\Role;
use App\Events\TeamMemberAssigned;
use App\Models\Project;
use App\Models\ProjectTeamMember;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignTeamMember
{
    public function __construct(protected ActivityLogger $activity) {}

    public function handle(Project $project, User $user, Role $role, ?int $eventId = null): ProjectTeamMember
    {
        return DB::transaction(function () use ($project, $user, $role, $eventId) {
            $exists = ProjectTeamMember::query()
                ->where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->where('role', $role->value)
                ->where('event_id', $eventId)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'user_id' => 'This team member is already assigned in that role.',
                ]);
            }

            $member = ProjectTeamMember::query()->create([
                'organization_id' => $project->organization_id,
                'project_id' => $project->id,
                'user_id' => $user->id,
                'event_id' => $eventId,
                'role' => $role,
                'assigned_at' => now(),
            ]);

            $this->activity->log('team.assigned', $project, [
                'user_id' => $user->id,
                'role' => $role->value,
            ]);

            event(new TeamMemberAssigned($member));

            return $member;
        });
    }
}
