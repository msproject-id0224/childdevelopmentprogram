<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;

class MentorChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentor_can_access_communication_page()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);

        $response = $this->actingAs($mentor)->get(route('communication.index'));

        $response->assertStatus(200);
    }

    public function test_mentor_can_fetch_online_users()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant']);

        // Simulate participant activity
        DB::table('sessions')->insert([
            'id' => 'session1',
            'user_id' => $participant->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla',
            'payload' => 'test',
            'last_activity' => now()->timestamp,
        ]);

        $response = $this->actingAs($mentor)->getJson(route('api.online-users'));

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $participant->id]);
    }

    public function test_mentor_can_see_message_content()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant']);

        $message = ChatMessage::create([
            'sender_id' => $participant->id,
            'receiver_id' => $mentor->id,
            'message' => 'Hello Mentor, this is a test message.',
            'status' => 'sent',
        ]);

        $response = $this->actingAs($mentor)->getJson(route('api.chat.index', $participant->id));

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Hello Mentor, this is a test message.']);
    }

    public function test_mentor_can_send_message()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $participant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($mentor)->postJson(route('api.chat.store'), [
            'receiver_id' => $participant->id,
            'message' => 'Hello Participant',
        ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $mentor->id,
            'receiver_id' => $participant->id,
            'message' => 'Hello Participant',
        ]);
    }
}
