<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RmdGodsPurposeTest extends TestCase
{
    use RefreshDatabase;

    public function test_gods_purpose_page_is_displayed()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($user)->get(route('rmd.gods-purpose'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Rmd/GodsPurpose')
        );
    }
}
