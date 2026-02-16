<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTest extends TestCase
{
    use RefreshDatabase;

    // public function test_session_driver_is_database()
    // {
    //     $this->assertEquals('database', config('session.driver'));
    // }

    public function test_user_remains_logged_in_across_requests()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        // Simulate a second request to ensure session persists
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }
}
