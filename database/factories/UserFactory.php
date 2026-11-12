<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'role_id' => fn () => Role::firstOrCreate(['code' => Role::OPERATOR], ['name' => 'Operador'])->id,
            'email' => fake()->unique()->safeEmail(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'password' => 'password',
            'phone' => '0991234567',
            'remember_token' => Str::random(10),
        ];
    }

    public function role(string $code): static
    {
        return $this->state(fn () => [
            'role_id' => Role::firstOrCreate(['code' => $code], ['name' => ucfirst(strtolower($code))])->id,
        ]);
    }

    public function admin(): static
    {
        return $this->role(Role::ADMIN);
    }
}
