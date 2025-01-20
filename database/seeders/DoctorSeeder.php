<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run()
    {
        Doctor::factory()
            ->create([
                'name' => 'Dr. Example',
                'crm' => '123456',
                'email' => 'doctor@example.com',
                'password' => bcrypt('asdasd'),
                'specialty_id' => Specialty::first()->id,
            ]);

        Doctor::factory(9)
            ->create();
    }
}
