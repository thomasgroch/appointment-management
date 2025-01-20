<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DoctorFactory extends Factory
{
    protected static ?string $password;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'crm' => $this->faker->numberBetween(1000, 99999),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'specialty_id' => Specialty::factory(),
        ];
    }
}
