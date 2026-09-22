<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Organization;
use App\Support\Identifiers;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LeadFactory extends Factory
{
    public function definition(): array
    {
        $org = Organization::query()->first()?->id ?? Organization::factory();

        return [
            'ulid' => (string) Str::ulid(),
            'organization_id' => $org,
            'lead_number' => 'LD-TEST-'.strtoupper(Str::random(6)),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('+91##########'),
            'city' => fake()->city(),
            'status' => LeadStatus::New,
            'wedding_date' => now()->addMonths(6),
        ];
    }
}
