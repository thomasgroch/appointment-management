<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Str;

class PatientFactory extends Factory
{
    protected static ?string $password;

    public function definition()
    {
        $faker = FakerFactory::create('pt_BR');

        return [
            'name' => $faker->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'cpf' => $faker->unique()->numerify('###.###.###-##'),
            'cep' => $faker->postcode(),
            'address' => $faker->address(),
            'number' => $faker->buildingNumber(),
        ];
    }
}
