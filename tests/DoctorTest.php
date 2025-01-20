<?php

namespace Tests;

use App\Models\Doctor;
use App\Models\Specialty;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DoctorTest extends ApiTester
{
    use RefreshDatabase;

    private function authenticateUser()
    {
        $doctor = Doctor::factory()->create();
        Sanctum::actingAs($doctor);
        return $doctor;
    }

    #[Test]
    public function it_lists_all_doctors()
    {
        $this->authenticateUser();
        Doctor::factory()->create();

        $response = $this->getJson('/api/doctors');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'email', 'crm', 'specialty_id']
                ]
            ]);
    }

    #[Test]
    public function it_creates_a_new_doctor_given_valid_parameters()
    {
        $this->authenticateUser();
        $data = [
            'name' => 'Dr. John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'crm' => 123456,
            'specialty_id' => Specialty::factory()->create()->id,
        ];
        $response = $this->postJson('/api/doctors', $data);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'email', 'crm', 'specialty_id']
            ])
            ->assertJsonPath('data.email', $data['email']);

        $this->assertDatabaseHas('doctors', [
            'email' => $data['email'],
            'crm' => $data['crm'],
        ]);
    }

    #[Test]
    public function it_displays_a_doctor()
    {
        $this->authenticateUser();
        $doctor = Doctor::factory()->create([
            'name' => 'Dr. John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
            'crm' => 123456,
            'specialty_id' => Specialty::factory()->create()->id,
        ]);
        $response = $this->getJson("/api/doctors/{$doctor->id}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'email', 'crm', 'specialty_id']
            ]);
    }

    #[Test]
    public function it_404s_if_a_doctor_is_not_found()
    {
        $this->authenticateUser();
        $response = $this->getJson('api/doctors/x');
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Resource not found'
        ]);
    }

    #[Test]
    public function it_throws_a_422_if_a_new_doctor_request_fails_validation()
    {
        $this->authenticateUser();
        $response = $this->postJson('api/doctors', []);
        $response->assertStatus(422);
    }

    #[Test]
    public function it_modifies_single_field_of_a_doctor()
    {
        $this->authenticateUser();
        $doctor = Doctor::factory()->create();

        $data = [
            'name' => 'Updated Name',
        ];

        $response = $this->putJson("api/doctors/{$doctor->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'email', 'crm', 'specialty_id']
            ])
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.email', $doctor->email);

        $this->assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'name' => 'Updated Name',
            'email' => $doctor->email,
        ]);
    }

    #[Test]
    public function it_modifies_all_fields_of_a_doctor()
    {
        $this->authenticateUser();
        $doctor = Doctor::factory()->create();
        $newSpecialty = Specialty::factory()->create();

        $data = [
            'name' => 'Dr. Jane Smith',
            'email' => 'jane.smith@example.com',
            'password' => 'newpassword123',
            'crm' => 987654,
            'specialty_id' => $newSpecialty->id,
        ];

        $response = $this->putJson("api/doctors/{$doctor->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'email', 'crm', 'specialty_id']
            ])
            ->assertJsonPath('data.name', $data['name'])
            ->assertJsonPath('data.email', $data['email'])
            ->assertJsonPath('data.crm', $data['crm'])
            ->assertJsonPath('data.specialty_id', $data['specialty_id']);

        $this->assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'crm' => $data['crm'],
            'specialty_id' => $data['specialty_id'],
        ]);
    }

    #[Test]
    public function it_removes_a_doctor()
    {
        $this->authenticateUser();
        $doctor = Doctor::factory()->create();

        $response = $this->deleteJson("api/doctors/{$doctor->id}");

        $response->assertStatus(204)
            ->assertNoContent();
    }
}
