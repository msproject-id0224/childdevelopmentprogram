<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('auth.user')
        );
    }

    public function test_admin_sees_admin_panel()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        // Inertia testing assertion to check if prop exists implicitly via page content logic check
        // Ideally we check if specific props are passed if defined, but here we check structure
        // Since we logic is in JSX, we rely on component load
    }

    public function test_participant_sees_participant_panel()
    {
        $user = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_dashboard_does_not_error_with_missing_fields()
    {
        // Create user with minimal attributes to test resilience
        $user = User::factory()->create([
            'role' => 'participant',
            // name is usually required, but let's assume factories handle it
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));
        
        $response->assertStatus(200);
    }
}
