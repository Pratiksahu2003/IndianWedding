<?php

use App\Models\Organization;
use App\Support\SocialPlatforms;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Organization::query()->each(function (Organization $organization): void {
            SocialPlatforms::syncForOrganization($organization);
        });
    }

    public function down(): void
    {
        // Keys are harmless if left in place; no rollback required.
    }
};
