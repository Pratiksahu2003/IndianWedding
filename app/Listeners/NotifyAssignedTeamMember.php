<?php

namespace App\Listeners;

use App\Events\TeamMemberAssigned;
use App\Notifications\TeamAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAssignedTeamMember implements ShouldQueue
{
    public function handle(TeamMemberAssigned $event): void
    {
        $event->member->user?->notify(new TeamAssignedNotification($event->member));
    }
}
