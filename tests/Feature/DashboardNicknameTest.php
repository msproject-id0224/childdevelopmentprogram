<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardNicknameTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_for_user_with_nickname()
    {
        $user = User::factory()->create([
            'role' => 'participant',
            'first_name' => 'John',
            'nickname' => 'Johnny',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('auth.user', fn ($prop) => $prop
                ->where('nickname', 'Johnny')
                ->etc()
            )
        );
    }

    public function test_dashboard_renders_for_user_without_nickname()
    {
        $user = User::factory()->create([
            'role' => 'participant',
            'first_name' => 'Jane',
            'nickname' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('auth.user', fn ($prop) => $prop
                ->where('nickname', null)
                ->where('first_name', 'Jane')
                ->etc()
            )
        );
    }
}
