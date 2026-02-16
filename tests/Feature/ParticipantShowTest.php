<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ParticipantShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_show_displays_nickname()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = User::factory()->create([
            'role' => 'participant',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'nickname' => 'Janey',
        ]);

        $response = $this->actingAs($admin)->get(route('participants.show', $participant->id));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Participant/Show')
            ->has('participant')
            ->where('participant.nickname', 'Janey')
        );
    }
}
