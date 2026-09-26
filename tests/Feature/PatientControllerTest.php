<?php

namespace Tests\Feature;

use App\User;
use App\Patients;
use App\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser($type = 'general')
    {
        return User::factory()->create(['user_type' => $type]);
    }

    protected function makePatient($id, $name = 'Test Patient')
    {
        // Patients has no $fillable: hydrate manually instead of ::create().
        $patient = new Patients;
        $patient->id = $id;
        $patient->name = $name;
        $patient->address = 'Test Address';
        $patient->contactnumber = '771234567';
        $patient->sex = 'Male';
        $patient->bod = '1990-01-01';
        $patient->occupation = 'Teacher';
        $patient->telephone = '777777777';
        $patient->nic = 'NIC' . $id;
        $patient->save();
        return $patient->fresh();
    }

    protected function makeAppointment($patientId, $number = 1)
    {
        // Appointment has no $fillable: hydrate manually instead of ::create().
        $app = new Appointment;
        $app->patient_id = $patientId;
        $app->number = $number;
        $app->save();
        return $app;
    }

    public function test_guest_cannot_access_patient_list()
    {
        $this->get('/patient')->assertRedirect('/login');
    }

    public function test_staff_can_view_patient_list()
    {
        $user = $this->createUser('general');
        $this->actingAs($user)->get('/patient')->assertStatus(200);
    }

    public function test_patient_can_be_registered()
    {
        $user = $this->createUser('general');
        $response = $this->actingAs($user)->post('/patientregister', [
            'reg_pname' => 'Test Patient',
            'reg_paddress' => 'Test Address',
            'reg_psex' => 'Male',
            'reg_pbd' => '1990-01-01',
            'reg_poccupation' => 'Teacher',
            'reg_ptel' => '777777777',
            'reg_pnic' => '1234567890',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('patients', ['name' => 'Test Patient']);
    }

    public function test_validate_appointment_number_returns_patient()
    {
        $user = $this->createUser('doctor');
        $patient = $this->makePatient(1000001);
        $this->makeAppointment($patient->id, 1);

        $response = $this->actingAs($user)->postJson('/validateAppNum', ['number' => 1]);
        $response->assertStatus(200)->assertJson(['exist' => true]);
    }

    public function test_validate_appointment_number_returns_false_for_invalid()
    {
        $user = $this->createUser('doctor');
        $response = $this->actingAs($user)->postJson('/validateAppNum', ['number' => 9999]);
        $response->assertStatus(200)->assertJson(['exist' => false]);
    }

    public function test_doctor_can_soft_delete_patient()
    {
        $user = $this->createUser('doctor');
        $patient = $this->makePatient(1000002, 'To Delete');

        $this->actingAs($user)->get("/patient-delete/{$patient->id}/delete");
        $this->assertSoftDeleted('patients', ['id' => $patient->id]);
    }
}
