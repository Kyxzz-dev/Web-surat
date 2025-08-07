<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $bidangs = ['Intelijen', 'Pengawasan', 'Umum', 'Perjalanan'];

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'nip' => fake()->unique()->numerify('##################'),
            'phone' => fake()->phoneNumber(),
            'bidang' => fake()->randomElement($bidangs),
            'email_verified_at' => now(),
            'password' => bcrypt('admin'), // Atau kamu bisa set password default
            'remember_token' => Str::random(10),
            'role' => null, // di-set manual dari seeder
            'admin_id' => null, // di-set manual juga
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
