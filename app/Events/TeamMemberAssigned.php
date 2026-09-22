<?php

namespace App\Events;

use App\Models\ProjectTeamMember;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamMemberAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(public ProjectTeamMember $member) {}
}
