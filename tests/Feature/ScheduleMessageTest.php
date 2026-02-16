<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\ScheduleMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentor_can_send_message()
    {
        $user = User::factory()->create(['role' => 'mentor']);
        $schedule = Schedule::create([
            'name' => 'Test Schedule',
            'description' => 'Test Description',
            'date' => '2023-01-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'priority' => 'high',
            'status' => 'scheduled',
            'pic' => 'Test PIC'
        ]);

        $response = $this->actingAs($user)->postJson(route('api.schedule-messages.store'), [
            'schedule_id' => $schedule->id,
            'message' => 'Hello Admin, need help.',
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Message sent successfully.']);

        $this->assertDatabaseHas('schedule_messages', [
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'message' => 'Hello Admin, need help.',
            'is_read' => false,
        ]);
    }

    public function test_send_message_validation()
    {
        $user = User::factory()->create(['role' => 'mentor']);

        $response = $this->actingAs($user)->postJson(route('api.schedule-messages.store'), [
            'schedule_id' => 999, // Invalid ID
            'message' => '', // Empty message
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['schedule_id', 'message']);
    }

    public function test_admin_can_get_unread_messages()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $schedule = Schedule::create([
            'name' => 'Test Schedule',
            'date' => '2023-01-01',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'description' => 'Test',
            'pic' => 'Test PIC',
            'status' => 'scheduled',
            'priority' => 'medium'
        ]);

        ScheduleMessage::create([
            'user_id' => $mentor->id,
            'schedule_id' => $schedule->id,
            'message' => 'Unread Message',
            'is_read' => false,
            'is_archived' => false,
        ]);

        ScheduleMessage::create([
            'user_id' => $mentor->id,
            'schedule_id' => $schedule->id,
            'message' => 'Read Message',
            'is_read' => true,
            'is_archived' => false,
        ]);

        ScheduleMessage::create([
            'user_id' => $mentor->id,
            'schedule_id' => $schedule->id,
            'message' => 'Archived Unread Message',
            'is_read' => false,
            'is_archived' => true,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->getJson(route('api.admin.schedule-messages.unread'));

        $response->assertStatus(200)
            ->assertJsonCount(1); // Only the unread, unarchived message
    }

    public function test_admin_can_mark_message_as_read()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $schedule = Schedule::create([
            'name' => 'Test Schedule',
            'date' => '2023-01-01',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'description' => 'Test',
            'pic' => 'Test PIC',
            'status' => 'scheduled',
            'priority' => 'medium'
        ]);

        $message = ScheduleMessage::create([
            'user_id' => $mentor->id,
            'schedule_id' => $schedule->id,
            'message' => 'Test message',
            'is_read' => false,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->patchJson(route('api.admin.schedule-messages.read', $message->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('schedule_messages', [
            'id' => $message->id,
            'is_read' => true,
        ]);
    }

    public function test_admin_can_archive_message()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $schedule = Schedule::create([
            'name' => 'Test Schedule',
            'date' => '2023-01-01',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'description' => 'Test',
            'pic' => 'Test PIC',
            'status' => 'scheduled',
            'priority' => 'medium'
        ]);

        $message = ScheduleMessage::create([
            'user_id' => $mentor->id,
            'schedule_id' => $schedule->id,
            'message' => 'Test message',
            'is_archived' => false,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->patchJson(route('api.admin.schedule-messages.archive', $message->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('schedule_messages', [
            'id' => $message->id,
            'is_archived' => true,
        ]);
    }

    public function test_user_can_get_messages_by_schedule()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $schedule = Schedule::create([
            'name' => 'Test Schedule',
            'date' => '2023-01-01',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'description' => 'Test',
            'pic' => 'Test PIC',
            'status' => 'scheduled',
            'priority' => 'medium'
        ]);

        ScheduleMessage::create([
            'user_id' => $mentor->id,
            'schedule_id' => $schedule->id,
            'message' => 'Message 1',
        ]);

        ScheduleMessage::create([
            'user_id' => $mentor->id,
            'schedule_id' => $schedule->id,
            'message' => 'Message 2',
        ]);

        $user = User::factory()->create(['role' => 'mentor']);

        $response = $this->actingAs($user)->getJson(route('api.schedules.messages', $schedule->id));

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }
}
