<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RmdTrueSuccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class RmdTrueSuccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_true_success_page_is_displayed()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($user)->get(route('rmd.true-success'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Rmd/TrueSuccess')
        );
    }

    public function test_true_success_data_can_be_saved()
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'participant']);
        $file = UploadedFile::fake()->create('learning.jpg');

        $response = $this->actingAs($user)->post(route('rmd.true-success.store'), [
            'successful_life_definition' => 'Hidup yang taat',
            'general_success_measure' => 'Kaya dan terkenal',
            'luke_2_52_growth' => 'Hikmat dan fisik',
            'philippians_2_5_10_actions' => 'Merendahkan diri',
            'jesus_success_vs_society' => 'Tidak menurut dunia',
            'god_opinion_on_jesus' => 'Anak yang Kukasihi',
            'new_learning_text' => 'Sukses adalah ketaatan',
            'new_learning_image' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('rmd_true_successes', [
            'user_id' => $user->id,
            'successful_life_definition' => 'Hidup yang taat',
            'general_success_measure' => 'Kaya dan terkenal',
            'luke_2_52_growth' => 'Hikmat dan fisik',
        ]);

        $trueSuccess = RmdTrueSuccess::where('user_id', $user->id)->first();
        $this->assertNotNull($trueSuccess->new_learning_image_path);
        Storage::disk('public')->assertExists($trueSuccess->new_learning_image_path);
    }

    public function test_true_success_page_displays_saved_data()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        RmdTrueSuccess::create([
            'user_id' => $user->id,
            'successful_life_definition' => 'Saved definition',
        ]);

        $response = $this->actingAs($user)->get(route('rmd.true-success'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Rmd/TrueSuccess')
            ->where('trueSuccess.successful_life_definition', 'Saved definition')
        );
    }
}
