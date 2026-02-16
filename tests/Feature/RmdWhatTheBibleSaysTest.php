<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RmdBibleReflection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RmdWhatTheBibleSaysTest extends TestCase
{
    use RefreshDatabase;

    public function test_what_the_bible_says_page_is_displayed()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($user)->get(route('rmd.what-the-bible-says'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Rmd/WhatTheBibleSays')
        );
    }

    public function test_what_the_bible_says_data_can_be_saved()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($user)->post(route('rmd.what-the-bible-says.store'), [
            'jeremiah_29_11_who_knows' => 'God knows',
            'jeremiah_29_11_plans' => 'Plans for good',
            'favorite_verse' => 'Jeremiah 29:11',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('rmd_bible_reflections', [
            'user_id' => $user->id,
            'jeremiah_29_11_who_knows' => 'God knows',
            'jeremiah_29_11_plans' => 'Plans for good',
            'favorite_verse' => 'Jeremiah 29:11',
        ]);
    }

    public function test_what_the_bible_says_page_displays_saved_data()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        RmdBibleReflection::create([
            'user_id' => $user->id,
            'jeremiah_29_11_who_knows' => 'Saved Answer',
        ]);

        $response = $this->actingAs($user)->get(route('rmd.what-the-bible-says'));

        $response->assertInertia(fn ($page) => $page
            ->component('Rmd/WhatTheBibleSays')
            ->where('reflection.jeremiah_29_11_who_knows', 'Saved Answer')
        );
    }

    public function test_what_the_bible_says_leadership_and_image_can_be_saved()
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'participant']);
        $file = UploadedFile::fake()->create('learning.jpg');

        $response = $this->actingAs($user)->post(route('rmd.what-the-bible-says.store'), [
            'leadership_c1' => 'Christ',
            'leadership_c2' => 'Community',
            'chapter_learning_text' => 'I learned a lot.',
            'chapter_learning_image' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('rmd_bible_reflections', [
            'user_id' => $user->id,
            'leadership_c1' => 'Christ',
            'leadership_c2' => 'Community',
            'chapter_learning_text' => 'I learned a lot.',
        ]);

        $reflection = RmdBibleReflection::where('user_id', $user->id)->first();
        $this->assertNotNull($reflection->chapter_learning_image_path);
        Storage::disk('public')->assertExists($reflection->chapter_learning_image_path);
    }

    public function test_what_the_bible_says_image_validation()
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'participant']);
        
        // Test non-image file
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $response = $this->actingAs($user)->post(route('rmd.what-the-bible-says.store'), [
            'chapter_learning_image' => $file,
        ]);
        $response->assertSessionHasErrors('chapter_learning_image');

        // Test too large image ( > 5MB)
        $largeFile = UploadedFile::fake()->create('large.jpg', 6000); // 6MB
        $response = $this->actingAs($user)->post(route('rmd.what-the-bible-says.store'), [
            'chapter_learning_image' => $largeFile,
        ]);
        $response->assertSessionHasErrors('chapter_learning_image');
    }
}
