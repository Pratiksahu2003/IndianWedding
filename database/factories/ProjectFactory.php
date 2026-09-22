<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Customer;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ulid' => (string) Str::ulid(),
            'organization_id' => Organization::factory(),
            'customer_id' => Customer::factory(),
            'project_number' => 'PR-'.strtoupper(Str::random(6)),
            'title' => fake()->name().' Wedding',
            'status' => ProjectStatus::BookingConfirmed,
            'total_amount' => 25000000,
            'booked_at' => now(),
        ];
    }
}
