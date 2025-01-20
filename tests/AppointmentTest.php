<?php

namespace Tests;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected $doctor;
    protected $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = Doctor::factory()->create();
        $this->patient = Patient::factory()->create();
    }

    #[Test]
    public function it_lists_all_appointments()
    {
        Appointment::factory()->count(3)->create([
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id
        ]);

        $response = $this->getJson('/api/appointments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'patient_id', 'doctor_id', 'appointment_time']
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function it_creates_a_new_appointment_given_valid_parameters()
    {
        $data = [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_time' => '2023-12-03 12:00:00',
        ];

        $response = $this->postJson('/api/appointments', $data);

        $response->assertStatus(201)
            ->assertJson([
                'data' => $data
            ]);
    }

    #[Test]
    public function it_displays_an_appointment()
    {
        $appointment = Appointment::factory()->create([
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'appointment_time' => '2023-12-01 10:00:00'
        ]);

        $response = $this->getJson("/api/appointments/{$appointment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $appointment->id,
                    'patient_id' => $this->patient->id,
                    'doctor_id' => $this->doctor->id,
                    'appointment_time' => '2023-12-01 10:00:00'
                ]
            ]);
    }

    #[Test]
    public function it_404s_if_an_appointment_is_not_found()
    {
        $response = $this->getJson('/api/appointments/999');
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Resource not found'
        ]);
    }

    #[Test]
    public function it_throws_a_422_if_a_new_appointment_request_fails_validation()
    {
        $response = $this->postJson('/api/appointments', [
            'patient_id' => '',
            'doctor_id' => '',
            'appointment_time' => 'invalid datetime'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'patient_id',
                'doctor_id',
                'appointment_time'
            ]);
    }

    #[Test]
    public function it_modifies_single_field_of_appointment()
    {
        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_time' => '2023-12-04 13:00:00'
        ]);

        $response = $this->patchJson("/api/appointments/{$appointment->id}", [
            'appointment_time' => '2023-12-04 14:00:00'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $appointment->id,
                    'appointment_time' => '2023-12-04 14:00:00'
                ]
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_time' => '2023-12-04 14:00:00'
        ]);
    }

    #[Test]
    public function it_modifies_all_fields_of_appointment()
    {
        $newDoctor = Doctor::factory()->create();
        $newPatient = Patient::factory()->create();

        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_time' => '2023-12-05 15:00:00'
        ]);

        $updateData = [
            'patient_id' => $newPatient->id,
            'doctor_id' => $newDoctor->id,
            'appointment_time' => '2023-12-06 16:00:00'
        ];

        $response = $this->putJson("/api/appointments/{$appointment->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => array_merge(['id' => $appointment->id], $updateData)
            ]);
    }

    #[Test]
    public function it_removes_an_appointment()
    {
        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id
        ]);

        $response = $this->deleteJson("/api/appointments/{$appointment->id}");

        $response->assertStatus(204)
            ->assertNoContent();
    }
}
