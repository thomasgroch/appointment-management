<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run()
    {
        Specialty::create([
            'name' => 'Cardiology',
        ]);

        Specialty::create([
            'name' => 'Orthopedics',
        ]);

        Specialty::create([
            'name' => 'Neurology',
        ]);

        Specialty::factory(7)->create();
    }
}
