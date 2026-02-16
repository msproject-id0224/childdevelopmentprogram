<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RmdAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_participants_can_access_rmd_module(): void
    {
        $participant = User::factory()->create([
            'role' => User::ROLE_PARTICIPANT,
        ]);

        $response = $this->actingAs($participant)->get(route('rmd.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($participant)->get(route('rmd.intro'));
        $response->assertStatus(200);

        $response = $this->actingAs($participant)->get(route('rmd.chapters'));
        $response->assertStatus(200);
    }

    public function test_mentors_cannot_access_rmd_module_and_are_logged(): void
    {
        $mentor = User::factory()->create([
            'role' => User::ROLE_MENTOR,
        ]);

        $response = $this->actingAs($mentor)->get(route('rmd.index'));
        $response->assertStatus(403);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $mentor->id,
            'action' => 'UNAUTHORIZED_ACCESS_ATTEMPT',
            'target_type' => 'RMD_MODULE',
        ]);
    }

    public function test_admins_cannot_access_rmd_module_and_are_logged(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('rmd.index'));
        $response->assertStatus(403);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'UNAUTHORIZED_ACCESS_ATTEMPT',
            'target_type' => 'RMD_MODULE',
        ]);
    }

    public function test_access_denial_works_across_rmd_routes(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $routes = [
            route('rmd.index'),
            route('rmd.intro'),
            route('rmd.profile'),
            route('rmd.quiz'),
            route('rmd.chapters'),
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)->get($route)->assertStatus(403);
        }
        
        // Count should match number of attempts
        $this->assertDatabaseCount('audit_logs', count($routes));
    }
}
