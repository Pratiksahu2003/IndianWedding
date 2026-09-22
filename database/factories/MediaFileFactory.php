<?php

namespace Database\Factories;

use App\Enums\FileKind;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MediaFileFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->lexify('file-??????').'.jpg';

        return [
            'ulid' => (string) Str::ulid(),
            'organization_id' => Organization::factory(),
            'project_id' => Project::factory(),
            'name' => $name,
            'original_name' => $name,
            'mime_type' => 'image/jpeg',
            'size' => 2048,
            'disk' => 'local',
            'path' => 'org/demo/'.$name,
            'kind' => FileKind::Edited,
            'visibility' => 'private',
            'status' => 'ready',
        ];
    }
}
