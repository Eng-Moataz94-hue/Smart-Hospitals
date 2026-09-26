<?php

namespace Tests\Feature;

use App\User;
use App\Patients;
use App\Invoice;
use App\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser($type = 'general')
    {
        return User::factory()->create(['user_type' => $type]);
    }

    protected function makePatient($id, $name = 'Test Patient')
    {
        $p = new Patients;
        $p->id = $id;
        $p->name = $name;
        $p->address = 'Test';
        $p->sex = 'Male';
        $p->bod = '1990-01-01';
        $p->occupation = 'Teacher';
        $p->telephone = '777777777';
        $p->save();
        return $p->fresh();
    }

    public function test_staff_can_view_invoices_list()
    {
        $user = $this->createUser('general');
        $this->actingAs($user)->get('/invoices')->assertStatus(200);
    }

    public function test_invoice_can_be_created_manually()
    {
        $user = $this->createUser('general');
        $patient = $this->makePatient(1000010);

        $response = $this->actingAs($user)->post('/invoices', [
            'patient_id' => $patient->id,
            'items' => [
                ['description' => 'Test Item', 'quantity' => 2, 'unit_price' => 500],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', ['patient_id' => $patient->id]);
        $this->assertDatabaseHas('invoice_items', ['description' => 'Test Item']);
    }

    public function test_invoice_pdf_can_be_generated()
    {
        $user = $this->createUser('general');
        $patient = $this->makePatient(1000011);
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'invoice_type' => 'consultation',
            'patient_id' => $patient->id,
            'issued_at' => now(),
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Test',
            'quantity' => 1,
            'unit_price' => 1000,
        ]);

        $response = $this->actingAs($user)->get("/invoices/{$invoice->id}/pdf");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_payment_can_be_recorded()
    {
        $user = $this->createUser('general');
        $patient = $this->makePatient(1000012);
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'invoice_type' => 'consultation',
            'patient_id' => $patient->id,
            'issued_at' => now(),
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Test',
            'quantity' => 1,
            'unit_price' => 5000,
        ]);

        $this->actingAs($user)->post("/invoices/{$invoice->id}/payment", [
            'amount' => 2000,
            'method' => 'cash',
        ]);

        $invoice->refresh();
        $this->assertEquals(2000, $invoice->paid_amount);
        $this->assertEquals('partial', $invoice->status);
    }

    public function test_mark_paid_sets_status_to_paid()
    {
        $user = $this->createUser('general');
        $patient = $this->makePatient(1000013);
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'invoice_type' => 'consultation',
            'patient_id' => $patient->id,
            'issued_at' => now(),
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Test',
            'quantity' => 1,
            'unit_price' => 5000,
        ]);

        $this->actingAs($user)->post("/invoices/{$invoice->id}/paid");

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(5000, $invoice->paid_amount);
    }

    public function test_admin_can_view_monthly_report()
    {
        $user = $this->createUser('admin');
        $this->actingAs($user)->get('/invoices/report/monthly')->assertStatus(200);
    }

    public function test_invoice_has_type_attribute()
    {
        $patient = $this->makePatient(1000014);
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'invoice_type' => 'appointment',
            'patient_id' => $patient->id,
            'issued_at' => now(),
        ]);

        $this->assertEquals('appointment', $invoice->invoice_type);
        $this->assertEquals('حجز موعد', $invoice->type_label);
    }

    public function test_create_for_patient_generates_new_invoice_each_call()
    {
        $patient = $this->makePatient(1000015);
        $ctrl = new \App\Http\Controllers\InvoiceController();
        $inv1 = $ctrl->createForPatient($patient->id, null, 'appointment');
        $inv2 = $ctrl->createForPatient($patient->id, null, 'consultation');

        $this->assertNotEquals($inv1->id, $inv2->id);
        $this->assertEquals('appointment', $inv1->invoice_type);
        $this->assertEquals('consultation', $inv2->invoice_type);
    }
}
