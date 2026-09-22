<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    use BelongsToOrganization;

    protected $guarded = [];
}
