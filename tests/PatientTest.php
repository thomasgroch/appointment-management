<?php

namespace Tests;

use App\Models\Patient;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser()
    {
        $patient = Patient::factory()->create();
        Sanctum::actingAs($patient);
        return $patient;
    }

    #[Test]
    public function it_lists_all_patients()
    {
        $this->authenticateUser();

        Patient::factory()->count(5)->create();
        $response = $this->getJson('/api/patients');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'email', 'cpf', 'cep', 'address', 'number']
                ]
            ]);
    }

    #[Test]
    public function it_creates_a_new_patient_given_valid_parameters()
    {
        $patientData = [
            'name' => 'Test Patient',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'cpf' => '12345678900',
            'cep' => '12345678',
            'address' => 'Test Address',
            'number' => '123',
        ];

        $response = $this->postJson('/api/patients', $patientData);
        $response->assertStatus(201);
        $data = [
            'name' => $patientData['name'],
            'cpf' => $patientData['cpf'],
            'cep' => $patientData['cep'],
            'address' => $patientData['address'],
            'number' => $patientData['number'],
            'email' => $patientData['email'],
        ];
        $response->assertJson(['data' => $data]);
    }

    #[Test]
    public function it_displays_a_patient()
    {
        $patient = Patient::factory()->create();
        $response = $this->getJson("/api/patients/{$patient->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $patient->id]);
    }

    #[Test]
    public function it_404s_if_a_patient_is_not_found()
    {
        $response = $this->getJson('/api/patients/999');
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Resource not found'
        ]);
    }

    #[Test]
    public function it_throws_a_422_if_a_new_patient_request_fails_validation()
    {
        $patientData = [
            'email' => 'invalidemail',
        ];
        $response = $this->postJson('/api/patients', $patientData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name', 'email', 'password', 'cpf', 'cep', 'address', 'number'
            ]);
    }

    #[Test]
    public function it_modifies_single_field_of_patient()
    {
        $patient = Patient::factory()->create();
        $response = $this->patchJson("/api/patients/{$patient->id}", [
            'name' => 'Updated Name'
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Updated Name']);
        $this->assertEquals('Updated Name', $patient->fresh()->name);
    }

    #[Test]
    public function it_modifies_all_fields_of_patient()
    {
        $patient = Patient::factory()->create();
        $patientData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'cpf' => '98765432101',
            'cep' => '87654321',
            'address' => 'New Address',
            'number' => '456',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];
        $response = $this->patchJson("/api/patients/{$patient->id}", $patientData);
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Updated Name']);
        $this->assertEquals('Updated Name', $patient->fresh()->name);
        $this->assertEquals('updated@example.com', $patient->fresh()->email);
        $this->assertEquals('98765432101', $patient->fresh()->cpf);
        $this->assertEquals('87654321', $patient->fresh()->cep);
        $this->assertEquals('New Address', $patient->fresh()->address);
        $this->assertEquals('456', $patient->fresh()->number);
    }

    #[Test]
    public function it_removes_a_patient()
    {
        $patient = Patient::factory()->create();

        $response = $this->deleteJson("/api/patients/{$patient->id}");

        $response->assertStatus(204)
            ->assertNoContent();
    }
}
