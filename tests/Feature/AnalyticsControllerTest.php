<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_statistics()
    {
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user)->get('/stats')->assertStatus(200);
    }

    public function test_general_user_cannot_view_statistics()
    {
        $user = User::factory()->create(['user_type' => 'general']);
        $response = $this->actingAs($user)->get('/stats');
        $response->assertStatus(302);
    }

    public function test_statistics_accepts_year_parameter()
    {
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user)->get('/stats?year=2025')->assertStatus(200);
    }
}
