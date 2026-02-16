<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Schedule;
use App\Models\ScheduleMessage;

class MentorCommunicationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentor_can_access_communication_page()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $response = $this->actingAs($mentor)->get(route('communication.index'));
        $response->assertStatus(200);
    }

    public function test_mentor_can_access_schedule_messages_api()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $response = $this->actingAs($mentor)->getJson(route('api.admin.schedule-messages.unread'));
        $response->assertStatus(200);
    }

    public function test_participant_cannot_access_communication_page()
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $response = $this->actingAs($participant)->get(route('communication.index'));
        $response->assertStatus(403);
    }

    public function test_participant_cannot_access_schedule_messages_api()
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $response = $this->actingAs($participant)->getJson(route('api.admin.schedule-messages.unread'));
        $response->assertStatus(403);
    }

    public function test_mentor_can_mark_message_as_read()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $user = User::factory()->create();
        $schedule = Schedule::create([
            'name' => 'Test Schedule',
            'date' => '2026-01-01',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'priority' => 'medium',
            'pic' => 'Test PIC',
            'description' => 'Test Description',
        ]);
        
        $message = ScheduleMessage::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'message' => 'Test message',
            'is_read' => false,
        ]);

        $response = $this->actingAs($mentor)->patchJson(route('api.admin.schedule-messages.read', $message->id));
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('schedule_messages', [
            'id' => $message->id,
            'is_read' => true,
        ]);
    }
}
