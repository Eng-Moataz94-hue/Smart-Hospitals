<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser($type = 'general', $fingerprint = null)
    {
        return User::factory()->create([
            'user_type' => $type,
            'fingerprint' => $fingerprint,
        ]);
    }

    public function test_user_can_view_my_attendance()
    {
        $user = $this->createUser('general');
        $this->actingAs($user)->get('/myattend')->assertStatus(200);
    }

    public function test_admin_can_view_more_attendance()
    {
        $user = $this->createUser('admin');
        $this->actingAs($user)->get('/attendmore')->assertStatus(200);
    }

    public function test_general_user_cannot_view_more_attendance()
    {
        $user = $this->createUser('general');
        $response = $this->actingAs($user)->get('/attendmore');
        $response->assertStatus(302);
    }

    public function test_mark_attendance_creates_record()
    {
        $user = $this->createUser('general', 42);
        $this->withoutMiddleware(\App\Http\Middleware\RedirectIfAuthenticated::class);
        $this->getJson('/attendance?' . http_build_query([
            'finger' => 42,
            'time' => now()->toDateTimeString(),
        ]))->assertStatus(200);

        $this->assertDatabaseHas('attendances', ['user_id' => $user->id]);
    }
}
