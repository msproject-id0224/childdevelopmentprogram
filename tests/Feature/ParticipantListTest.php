<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ParticipantListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_participant_list()
    {
        // Create Admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);

        // Create Participants
        $participant1 = User::factory()->create([
            'role' => 'participant',
            'first_name' => 'Participant',
            'last_name' => 'One',
            'email' => 'p1@test.com',
        ]);
        
        $participant2 = User::factory()->create([
            'role' => 'participant',
            'first_name' => 'Participant',
            'last_name' => 'Two',
            'email' => 'p2@test.com',
        ]);

        // Create Mentor (should not be in list)
        $mentor = User::factory()->create([
            'role' => 'mentor',
            'email' => 'm1@test.com',
        ]);

        $response = $this->actingAs($admin)
                         ->get(route('participants.index'));

        $response->assertStatus(200);
        
        // Assert Inertia response has participants
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Participant/Index')
            ->has('participants.data', 2)
            ->where('participants.data.0.role', 'participant')
            ->where('participants.data.1.role', 'participant')
        );
    }
}
