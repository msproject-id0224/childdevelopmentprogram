<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\ChatMessage;

class AdvancedChatFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_file_attachment()
    {
        Storage::fake('public');
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        // Use create instead of image to avoid GD dependency if missing
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($sender)->postJson(route('api.chat.store'), [
            'receiver_id' => $receiver->id,
            'message' => 'Check this out',
            'file' => $file,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $sender->id,
            'attachment_type' => 'application/pdf',
        ]);
        
        // Assert file was stored
        Storage::disk('public')->assertExists('chat-attachments/' . $file->hashName());
    }

    public function test_user_can_flag_message()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $message = ChatMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Bad message',
            'status' => 'sent',
        ]);

        $response = $this->actingAs($receiver)->patchJson(route('api.chat.flag', $message->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('chat_messages', [
            'id' => $message->id,
            'is_flagged' => true,
        ]);
    }

    public function test_typing_event_broadcast()
    {
        \Illuminate\Support\Facades\Event::fake();
        
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $response = $this->actingAs($sender)->postJson(route('api.chat.typing'), [
            'receiver_id' => $receiver->id
        ]);

        $response->assertStatus(200);
        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\MessageTyping::class);
    }

    public function test_unread_count_api()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Send 3 messages to $user
        ChatMessage::create([
            'sender_id' => $otherUser->id,
            'receiver_id' => $user->id,
            'message' => 'Msg 1',
            'status' => 'sent',
            'is_read' => false
        ]);
        ChatMessage::create([
            'sender_id' => $otherUser->id,
            'receiver_id' => $user->id,
            'message' => 'Msg 2',
            'status' => 'sent',
            'is_read' => false
        ]);
        // Send 1 read message
        ChatMessage::create([
            'sender_id' => $otherUser->id,
            'receiver_id' => $user->id,
            'message' => 'Msg 3',
            'status' => 'sent',
            'is_read' => true
        ]);

        $response = $this->actingAs($user)->getJson(route('api.chat.unread-count'));

        $response->assertStatus(200)
                 ->assertJson(['count' => 2]);
    }
}
