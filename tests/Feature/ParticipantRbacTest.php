<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParticipantRbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_mentor_to_participant()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($admin)
            ->patch(route('participants.assign-mentor', $participant->id), [
                'mentor_id' => $mentor->id,
            ]);

        $response->assertRedirect();
        $this->assertEquals($mentor->id, $participant->fresh()->mentor_id);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'ASSIGN_PARTICIPANT',
            'user_id' => $admin->id,
            'target_id' => $participant->id,
        ]);
    }

    public function test_mentor_cannot_assign_mentor()
    {
        $mentor1 = User::factory()->create(['role' => 'mentor']);
        $mentor2 = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($mentor1)
            ->patch(route('participants.assign-mentor', $participant->id), [
                'mentor_id' => $mentor2->id,
            ]);

        $response->assertForbidden();
        $this->assertNull($participant->fresh()->mentor_id);
    }

    public function test_mentor_can_only_view_assigned_participants_in_index()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $assignedParticipant = User::factory()->create(['role' => 'participant', 'mentor_id' => $mentor->id]);
        $unassignedParticipant = User::factory()->create(['role' => 'participant']);
        $otherParticipant = User::factory()->create(['role' => 'participant', 'mentor_id' => User::factory()->create(['role' => 'mentor'])->id]);

        $response = $this->actingAs($mentor)->get(route('participants.index'));

        $response->assertOk();
        $response->assertSee($assignedParticipant->first_name);
        $response->assertDontSee($unassignedParticipant->first_name);
        $response->assertDontSee($otherParticipant->first_name);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'ACCESS_PARTICIPANT_LIST',
            'user_id' => $mentor->id,
        ]);
    }

    public function test_mentor_can_view_assigned_participant_details()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant', 'mentor_id' => $mentor->id]);

        $response = $this->actingAs($mentor)->get(route('participants.show', $participant->id));

        $response->assertOk();
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'VIEW_PARTICIPANT_DETAILS',
            'user_id' => $mentor->id,
            'target_id' => $participant->id,
        ]);
    }

    public function test_mentor_cannot_view_unassigned_participant_details()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($mentor)->get(route('participants.show', $participant->id));

        $response->assertForbidden();
    }

    public function test_mentor_cannot_create_participant()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);

        $response = $this->actingAs($mentor)->post(route('participants.store'), [
            'first_name' => 'Test',
            'email' => 'test@example.com',
            'role' => 'participant',
        ]);

        $response->assertForbidden();
    }

    public function test_mentor_cannot_update_participant()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant', 'mentor_id' => $mentor->id]);

        $response = $this->actingAs($mentor)->patch(route('participants.update', $participant->id), [
            'first_name' => 'Updated Name',
        ]);

        $response->assertForbidden();
    }

    public function test_mentor_cannot_delete_participant()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant', 'mentor_id' => $mentor->id]);

        $response = $this->actingAs($mentor)->delete(route('participants.destroy', $participant->id));

        $response->assertForbidden();
    }

    public function test_mentor_cannot_toggle_status()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant', 'mentor_id' => $mentor->id]);

        $response = $this->actingAs($mentor)->patch(route('participants.toggle-status', $participant->id));

        $response->assertForbidden();
    }

    public function test_mentor_can_add_note_to_assigned_participant()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant', 'mentor_id' => $mentor->id]);

        $response = $this->actingAs($mentor)->post(route('participants.notes.store', $participant->id), [
            'note' => 'Test Note',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('participant_notes', [
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->id,
            'note' => 'Test Note',
        ]);
    }

    public function test_mentor_cannot_add_note_to_unassigned_participant()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($mentor)->post(route('participants.notes.store', $participant->id), [
            'note' => 'Test Note',
        ]);

        $response->assertForbidden();
    }
}
