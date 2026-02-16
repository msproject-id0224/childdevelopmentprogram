<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoleTest extends TestCase
{
    // use RefreshDatabase; // Do not refresh if checking actual seeded data, but here we want to check logic.
    // However, if the user complains about a SPECIFIC user not working, we might need to check that user.
    // Since I cannot access the live database easily without potentially destroying it with RefreshDatabase,
    // I will write a test that creates a new admin user and verifies the flow.

    use RefreshDatabase;

    public function test_role_middleware_allows_admin()
    {
        $user = User::factory()->create(['role' => 'admin']);

        // Assuming there is a route protected by role:admin
        // Let's use a known admin route from web.php
        $response = $this->actingAs($user)->get(route('mentors.index'));
        
        $response->assertStatus(200);
    }

    public function test_role_middleware_denies_participant()
    {
        $user = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($user)->get(route('mentors.index'));
        
        $response->assertStatus(403);
    }
    
    public function test_specific_user_role_setup()
    {
        // This test simulates the user adminfirst@cdp.com if we were seeding it.
        // Since we are in a test environment with RefreshDatabase, we create it manually
        // to verify the Model logic works as expected.
        $user = User::factory()->create([
            'email' => 'adminfirst@cdp.com',
            'role' => 'admin'
        ]);

        $this->assertTrue($user->isAdmin());
        $this->assertEquals('admin', $user->role);
    }
}
