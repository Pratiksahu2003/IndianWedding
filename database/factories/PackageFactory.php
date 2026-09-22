<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PackageFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement(['Heritage', 'Atelier', 'Cinematic']).' '.fake()->word();
        return [
            'ulid' => (string) Str::ulid(),
            'organization_id' => Organization::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(3)),
            'description' => fake()->sentence(),
            'price' => 25000000,
            'is_public' => true,
            'is_active' => true,
        ];
    }
}
