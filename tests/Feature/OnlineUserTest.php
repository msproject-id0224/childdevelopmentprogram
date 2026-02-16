<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnlineUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_online_users()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'role' => 'participant',
        ]);

        $otherUser = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'role' => 'mentor',
        ]);

        // Simulate session activity for otherUser
        \DB::table('sessions')->insert([
            'id' => 'session123',
            'user_id' => $otherUser->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0',
            'payload' => 'payload',
            'last_activity' => now()->timestamp,
        ]);

        $response = $this->actingAs($user)->getJson(route('api.online-users'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'role',
                'avatar',
                'status',
                'last_active',
            ]
        ]);
        
        // Verify name concatenation
        $data = $response->json();
        $this->assertEquals('Jane Doe', $data[0]['name']);
    }
}
