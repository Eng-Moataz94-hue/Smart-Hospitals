<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicineControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser($type = 'pharmacist')
    {
        return User::factory()->create(['user_type' => $type]);
    }

    public function test_pharmacist_can_view_issue_medicine_view()
    {
        $user = $this->createUser('pharmacist');
        $this->actingAs($user)->get('/issueMedicine/')->assertStatus(200);
    }

    public function test_general_user_cannot_view_issue_medicine_view()
    {
        $user = $this->createUser('general');
        $response = $this->actingAs($user)->get('/issueMedicine/');
        $response->assertStatus(302);
    }

    public function test_medicine_stocks_page_requires_pharmacist()
    {
        $user = $this->createUser('general');
        $response = $this->actingAs($user)->get('/medicine-stocks');
        $response->assertStatus(302);
    }

    public function test_pharmacist_can_view_medicine_stocks()
    {
        $user = $this->createUser('pharmacist');
        $this->actingAs($user)->get('/medicine-stocks')->assertStatus(200);
    }
}
