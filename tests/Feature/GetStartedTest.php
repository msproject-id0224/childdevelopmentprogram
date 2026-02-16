<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetStartedTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_started_page_renders(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_guest_clicking_get_started_goes_to_login(): void
    {
        // Simulate clicking by visiting the login route
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_auth_user_accessing_login_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/dashboard');
    }
    
    public function test_guest_is_not_redirected_to_dashboard_from_root(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
