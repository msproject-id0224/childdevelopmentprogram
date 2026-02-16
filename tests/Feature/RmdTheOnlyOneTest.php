<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RmdTheOnlyOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class RmdTheOnlyOneTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_only_one_page_is_displayed()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($user)->get(route('rmd.the-only-one'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Rmd/TheOnlyOne')
        );
    }

    public function test_the_only_one_data_can_be_saved()
    {
        $user = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($user)->post(route('rmd.the-only-one.store'), [
            'unique_traits' => 'Saya bisa menyanyi',
            'current_education_level' => 'SMP',
            'favorite_subject' => 'Matematika',
            'favorite_subject_reason' => 'Suka berhitung',
            'least_favorite_subject' => 'Sejarah',
            'least_favorite_subject_reason' => 'Banyak hafalan',
            'highest_score_subject' => 'Olahraga',
            'highest_score_value' => '90',
            'lowest_score_subject' => 'Seni',
            'lowest_score_value' => '70',
            'visual_checklist' => ['0' => 5, '1' => 3, '2' => 4],
            'auditory_checklist' => ['0' => 2, '1' => 4],
            'learned_aspects' => 'Belajar tentang gaya belajar',
            'aspects_to_improve' => 'Ingin lebih fokus',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('rmd_the_only_ones', [
            'user_id' => $user->id,
            'unique_traits' => 'Saya bisa menyanyi',
            'favorite_subject' => 'Matematika',
            'learned_aspects' => 'Belajar tentang gaya belajar',
            'aspects_to_improve' => 'Ingin lebih fokus',
        ]);

        $entry = RmdTheOnlyOne::where('user_id', $user->id)->first();
        $this->assertEquals(['0' => 5, '1' => 3, '2' => 4], $entry->visual_checklist);
        $this->assertEquals(['0' => 2, '1' => 4], $entry->auditory_checklist);
    }

    public function test_the_only_one_page_displays_saved_data()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        RmdTheOnlyOne::create([
            'user_id' => $user->id,
            'unique_traits' => 'Saved trait',
            'visual_checklist' => ['0' => 5, '1' => 3],
            'learned_aspects' => 'Saved learned',
        ]);

        $response = $this->actingAs($user)->get(route('rmd.the-only-one'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Rmd/TheOnlyOne')
            ->where('theOnlyOne.unique_traits', 'Saved trait')
            ->where('theOnlyOne.visual_checklist', ['0' => 5, '1' => 3])
            ->where('theOnlyOne.learned_aspects', 'Saved learned')
        );
    }
}
