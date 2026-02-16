<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ParticipantIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_index_displays_nickname()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = User::factory()->create([
            'role' => 'participant',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'nickname' => 'Johnny',
        ]);

        $response = $this->actingAs($admin)->get(route('participants.index'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Participant/Index')
            ->has('participants.data', 1)
            ->where('participants.data.0.nickname', 'Johnny')
        );
    }
}
