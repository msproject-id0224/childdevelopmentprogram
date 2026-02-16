<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Schedule;
use App\Models\ProfilePhotoRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTabsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_receives_correct_props()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create schedules
        Schedule::create([
            'name' => 'Future Event',
            'date' => now()->addDays(1)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'priority' => 'high',
            'pic' => 'Admin',
            'status' => 'scheduled',
            'description' => 'Test Description'
        ]);

        // Create pending photo request
        $user = User::factory()->create(['role' => 'participant']);
        ProfilePhotoRequest::create([
            'user_id' => $user->id,
            'photo_path' => 'photos/test.jpg',
            'status' => 'pending'
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('schedules')
                ->has('photoRequests', 1) // Should have 1 pending request
                ->where('photoRequests.0.user_id', $user->id)
            );
    }

    public function test_api_schedules_filtering()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create diverse schedules
        Schedule::create([
            'name' => 'Meeting A',
            'date' => '2025-01-01',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'priority' => 'high',
            'pic' => 'Admin',
            'status' => 'scheduled',
            'description' => 'Desc A'
        ]);

        Schedule::create([
            'name' => 'Meeting B',
            'date' => '2025-01-02',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'priority' => 'low',
            'pic' => 'Admin',
            'status' => 'completed',
            'description' => 'Desc B'
        ]);

        $this->actingAs($admin);

        // Test Filter by Date
        $response = $this->getJson(route('api.schedules', ['date' => '2025-01-01']));
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Meeting A');

        // Test Filter by Priority
        $response = $this->getJson(route('api.schedules', ['priority' => 'low']));
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Meeting B');

        // Test Filter by Status
        $response = $this->getJson(route('api.schedules', ['status' => 'completed']));
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Meeting B');

        // Test Search
        $response = $this->getJson(route('api.schedules', ['search' => 'Meeting A']));
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Meeting A');
    }
}
