<?php

namespace App\Models;

use App\Enums\Role;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTeamMember extends Model
{
    use BelongsToOrganization;

    protected $table = 'project_team';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'assigned_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
