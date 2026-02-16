<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ParticipantEditPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_participant_edit_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($admin)->get(route('participants.edit', $participant->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Participant/Edit')
            ->has('participant', fn (Assert $page) => $page
                ->where('id', $participant->id)
                ->where('first_name', $participant->first_name)
                ->etc()
            )
        );
    }

    public function test_mentor_cannot_access_participant_edit_page(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($mentor)->get(route('participants.edit', $participant->id));

        // Assuming mentor does not have permission to edit
        $response->assertStatus(403);
    }

    public function test_participant_cannot_access_other_participant_edit_page(): void
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $otherParticipant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($participant)->get(route('participants.edit', $otherParticipant->id));

        $response->assertStatus(403);
    }
}
