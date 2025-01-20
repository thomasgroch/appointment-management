<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run()
    {
        Patient::factory()->create([
            'name' => 'Patient Example',
            'email' => 'patient@example.com',
            'password' => bcrypt('asdasd'),
        ]);

        Patient::factory(9)->create();
    }
}
