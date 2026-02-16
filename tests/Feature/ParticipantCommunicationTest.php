<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ParticipantCommunicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_can_access_communication_page()
    {
        $participant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($participant)
                         ->get(route('participant.communication.index'));

        $response->assertStatus(200);
    }

    public function test_participant_can_search_users()
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $otherUser = User::factory()->create(['first_name' => 'John', 'role' => 'mentor']);

        $response = $this->actingAs($participant)
                         ->get(route('api.users.search', ['query' => 'John']));

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'John']);
    }

    public function test_participant_can_send_message()
    {
        $sender = User::factory()->create(['role' => 'participant']);
        $receiver = User::factory()->create(['role' => 'mentor']);

        $response = $this->actingAs($sender)
                         ->postJson(route('api.chat.store'), [
                             'receiver_id' => $receiver->id,
                             'message' => 'Hello Mentor!',
                         ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Hello Mentor!',
        ]);
    }

    public function test_participant_can_send_file_attachment()
    {
        Storage::fake('public');
        $sender = User::factory()->create(['role' => 'participant']);
        $receiver = User::factory()->create(['role' => 'mentor']);
        
        // Use create() instead of image() to avoid GD dependency
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($sender)
                         ->postJson(route('api.chat.store'), [
                             'receiver_id' => $receiver->id,
                             'file' => $file,
                         ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'attachment_type' => 'application/pdf',
        ]);
    }

    public function test_participant_can_fetch_messages()
    {
        $user1 = User::factory()->create(['role' => 'participant']);
        $user2 = User::factory()->create(['role' => 'mentor']);

        ChatMessage::create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Hello Participant',
            'status' => 'sent'
        ]);

        $response = $this->actingAs($user1)
                         ->get(route('api.chat.index', ['user' => $user2->id]));

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Hello Participant']);
    }

    public function test_online_status_is_updated()
    {
        $user = User::factory()->create(['role' => 'participant']);

        // Access a route protected by 'web' middleware which includes UpdateLastSeen
        $this->actingAs($user)->get(route('dashboard'));

        $this->assertNotNull($user->fresh()->last_seen_at);
    }
}
