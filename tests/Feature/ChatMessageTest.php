<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\ChatMessage;

class ChatMessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Skip OTP verification for tests if needed, or create verified users
    }

    public function test_user_can_send_message(): void
    {
        \Illuminate\Support\Facades\Event::fake();

        $sender = User::factory()->create(['role' => 'admin']);
        $receiver = User::factory()->create(['role' => 'mentor']);

        $response = $this->actingAs($sender)->postJson(route('api.chat.store'), [
            'receiver_id' => $receiver->id,
            'message' => 'Hello mentor!',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Hello mentor!',
            'status' => 'sent',
        ]);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\MessageSent::class);
    }

    public function test_user_can_fetch_chat_history_sorted_by_date(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $msg1 = ChatMessage::create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'First message',
            'created_at' => now()->subMinutes(10),
        ]);

        $msg2 = ChatMessage::create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Second message',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user1)->getJson(route('api.chat.index', $user2->id));

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonPath('0.message', 'First message')
            ->assertJsonPath('1.message', 'Second message');
    }

    public function test_user_can_mark_messages_as_read(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        ChatMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Unread message',
            'is_read' => false,
        ]);

        $response = $this->actingAs($receiver)->patchJson(route('api.chat.read', $sender->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'is_read' => true,
            'status' => 'delivered',
        ]);
    }

    public function test_user_can_get_unread_count_multiple_senders(): void
    {
        $user = User::factory()->create();
        $sender1 = User::factory()->create();
        $sender2 = User::factory()->create();

        ChatMessage::create([
            'sender_id' => $sender1->id,
            'receiver_id' => $user->id,
            'message' => 'Msg 1',
            'is_read' => false,
        ]);

        ChatMessage::create([
            'sender_id' => $sender2->id,
            'receiver_id' => $user->id,
            'message' => 'Msg 2',
            'is_read' => false,
        ]);

        ChatMessage::create([
            'sender_id' => $sender1->id,
            'receiver_id' => $user->id,
            'message' => 'Msg 3',
            'is_read' => true,
        ]);

        $response = $this->actingAs($user)->getJson(route('api.chat.unread-count'));

        $response->assertStatus(200)
            ->assertJson(['count' => 2]);
    }

    public function test_user_can_log_frontend_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('api.log-error'), [
            'type' => 'chat',
            'context' => 'testContext',
            'message' => 'Test error message',
            'stack' => 'Test stack trace',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }

    public function test_guest_cannot_send_message(): void
    {
        $receiver = User::factory()->create();

        $response = $this->postJson(route('api.chat.store'), [
            'receiver_id' => $receiver->id,
            'message' => 'Hello!',
        ]);

        $response->assertStatus(401);
    }

    public function test_message_validation(): void
    {
        $user = User::factory()->create();

        // Test empty message
        $response = $this->actingAs($user)->postJson(route('api.chat.store'), [
            'receiver_id' => $user->id,
            'message' => '',
        ]);
        $response->assertStatus(422);

        // Test non-existent receiver
        $response = $this->actingAs($user)->postJson(route('api.chat.store'), [
            'receiver_id' => 99999,
            'message' => 'Hello!',
        ]);
        $response->assertStatus(422);
    }
}
