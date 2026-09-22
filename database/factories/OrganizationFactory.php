<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company().' Studio';
        return [
            'ulid' => (string) Str::ulid(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'email' => fake()->companyEmail(),
            'phone' => fake()->numerify('+91##########'),
            'timezone' => 'Asia/Kolkata',
            'currency' => 'INR',
            'invoice_prefix' => 'INV',
            'is_active' => true,
        ];
    }
}
