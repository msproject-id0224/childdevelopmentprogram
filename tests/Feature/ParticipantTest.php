<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ParticipantNote;
use App\Models\ParticipantTask;
use App\Models\ParticipantMeeting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_list_page_is_displayed_for_authenticated_users(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('participants.index'));

        $response->assertStatus(200);
    }

    public function test_participant_list_page_is_displayed_for_mentor(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);

        $response = $this->actingAs($mentor)->get(route('participants.index'));

        $response->assertStatus(200);
    }

    public function test_mentor_cannot_access_participant_create(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);

        $response = $this->actingAs($mentor)->get(route('participants.create'));

        $response->assertStatus(403);
    }

    public function test_mentor_can_view_participant_detail_page(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant', 'mentor_id' => $mentor->id]);

        $response = $this->actingAs($mentor)->get(route('participants.show', $participant->id));

        $response->assertStatus(200);
        $response->assertSee($participant->first_name);
    }

    public function test_mentor_cannot_view_unassigned_participant(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant', 'mentor_id' => null]);

        $response = $this->actingAs($mentor)->get(route('participants.show', $participant->id));

        $response->assertStatus(403);
    }

    public function test_mentor_cannot_update_participant_status(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant', 'is_active' => true]);

        $response = $this->actingAs($mentor)->patchJson(route('participants.status.update', $participant->id), [
            'status' => 'inactive',
        ]);

        $response->assertStatus(403);
    }

    public function test_mentor_can_add_note_task_and_meeting(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant', 'mentor_id' => $mentor->id]);

        $noteResponse = $this->actingAs($mentor)->postJson(route('participants.notes.store', $participant->id), [
            'note' => 'Progress note',
        ]);
        $noteResponse->assertStatus(201);
        $this->assertDatabaseHas('participant_notes', [
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->id,
        ]);

        $taskResponse = $this->actingAs($mentor)->postJson(route('participants.tasks.store', $participant->id), [
            'title' => 'Task 1',
            'description' => 'Description',
            'due_date' => now()->addDays(3)->toDateString(),
        ]);
        $taskResponse->assertStatus(201);
        $this->assertDatabaseHas('participant_tasks', [
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->id,
            'title' => 'Task 1',
        ]);

        $meetingResponse = $this->actingAs($mentor)->postJson(route('participants.meetings.store', $participant->id), [
            'scheduled_at' => now()->addDays(1)->toDateTimeString(),
            'location' => 'Room A',
            'agenda' => 'Check-in',
        ]);
        $meetingResponse->assertStatus(201);
        $this->assertDatabaseHas('participant_meetings', [
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->id,
            'location' => 'Room A',
        ]);
    }

    public function test_participant_cannot_access_detail_or_actions(): void
    {
        $participantUser = User::factory()->create(['role' => 'participant']);
        $otherParticipant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($participantUser)->get(route('participants.show', $otherParticipant->id));
        $response->assertStatus(403);

        $response = $this->actingAs($participantUser)->postJson(route('participants.notes.store', $otherParticipant->id), [
            'note' => 'Not allowed',
        ]);
        $response->assertStatus(403);
    }

    public function test_participant_list_pagination_can_be_customized(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(15)->create(['role' => 'participant']);

        // Default pagination is 10
        $response = $this->actingAs($admin)->get(route('participants.index'));
        $response->assertStatus(200);
        
        // Custom pagination 5
        $response = $this->actingAs($admin)->get(route('participants.index', ['per_page' => 5]));
        $response->assertStatus(200);
        
        // Custom pagination 25 (should show all 15)
        $response = $this->actingAs($admin)->get(route('participants.index', ['per_page' => 25]));
        $response->assertStatus(200);
    }

    public function test_participant_list_can_be_searched(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'role' => 'participant'
        ]);
        User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'role' => 'participant'
        ]);

        $response = $this->actingAs($admin)->get(route('participants.index', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertSee('John');
        $response->assertDontSee('Jane');
    }

    public function test_participant_list_can_be_filtered_by_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create([
            'first_name' => 'Active User',
            'is_active' => true,
            'role' => 'participant'
        ]);
        User::factory()->create([
            'first_name' => 'Inactive User',
            'is_active' => false,
            'role' => 'participant'
        ]);

        $response = $this->actingAs($admin)->get(route('participants.index', ['status' => 'inactive']));

        $response->assertStatus(200);
        $response->assertSee('Inactive User');
        $response->assertDontSee('Active User');
    }

    public function test_participant_list_can_be_filtered_by_gender(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create([
            'first_name' => 'Male User',
            'gender' => 'Laki-laki',
            'role' => 'participant'
        ]);
        User::factory()->create([
            'first_name' => 'Female User',
            'gender' => 'Perempuan',
            'role' => 'participant'
        ]);

        $response = $this->actingAs($admin)->get(route('participants.index', ['gender' => 'Laki-laki']));

        $response->assertStatus(200);
        $response->assertSee('Male User');
        $response->assertDontSee('Female User');
    }

    public function test_participant_list_can_be_sorted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create([
            'first_name' => 'Alice',
            'role' => 'participant'
        ]);
        User::factory()->create([
            'first_name' => 'Bob',
            'role' => 'participant'
        ]);

        // Ascending
        $response = $this->actingAs($admin)->get(route('participants.index', [
            'sort_by' => 'first_name',
            'sort_direction' => 'asc'
        ]));
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Alice', 'Bob']);

        // Descending
        $response = $this->actingAs($admin)->get(route('participants.index', [
            'sort_by' => 'first_name',
            'sort_direction' => 'desc'
        ]));
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Bob', 'Alice']);
    }

    public function test_participant_creation_formats_id_number(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->post(route('participants.store'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'nickname' => 'Nick',
            'email' => 'test@example.com',
            'password' => 'password',
            'id_number' => '1',
            'date_of_birth' => null,
            'age' => null,
            'gender' => null,
            'education' => null,
            'age_group' => null,
            'height' => null,
            'weight' => null,
            'communication' => null,
        ]);
        
        $response->assertRedirect(route('participants.index'));
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'id_number' => 'ID-022400001'
        ]);
    }

    public function test_participant_update_formats_id_number(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'participant']);
        
        $response = $this->actingAs($admin)->patch(route('participants.update', $user->id), [
            'first_name' => 'Updated',
            'last_name' => 'User',
            'nickname' => 'Nick',
            'email' => $user->email,
            'id_number' => '99',
            'date_of_birth' => null,
            'age' => null,
            'gender' => null,
            'education' => null,
            'age_group' => null,
            'height' => null,
            'weight' => null,
            'communication' => null,
        ]);
        
        $response->assertRedirect(route('participants.index'));
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'id_number' => 'ID-022400099'
        ]);
    }
}
