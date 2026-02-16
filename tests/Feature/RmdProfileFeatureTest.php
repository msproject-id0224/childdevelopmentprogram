<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RmdProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RmdProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_rmd_profile_page_is_displayed()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($user)->get(route('rmd.profile'));

        $response->assertStatus(200);
    }

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($user)->post(route('rmd.profile.store'), [
            'first_name' => 'Test First Name',
            'last_name' => 'Test Last Name',
            'phone_number' => '08123456789',
            'address' => 'Test Address',
            'date_of_birth' => '2000-01-01',
            'gender' => 'Male',
            'first_filled_at' => now()->format('Y-m-d'),
            'first_filled_age' => 20,
            'first_filled_education' => 'SMA',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(); // Should redirect back

        $user->refresh();

        $this->assertSame('Test First Name', $user->first_name);
        $this->assertSame('Test Last Name', $user->last_name);
        $this->assertSame('08123456789', $user->phone_number);
        $this->assertSame('Test Address', $user->address);
        // Compare formatted date if it's a Carbon object
        $this->assertEquals('2000-01-01', $user->date_of_birth instanceof \Carbon\Carbon ? $user->date_of_birth->format('Y-m-d') : $user->date_of_birth);
        $this->assertSame('Male', $user->gender);

        $this->assertDatabaseHas('rmd_profiles', [
            'user_id' => $user->id,
            'first_filled_education' => 'SMA',
        ]);
    }

    public function test_profile_photo_can_be_uploaded()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        // Use create instead of image to avoid GD dependency
        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post(route('rmd.profile.store'), [
            'first_name' => 'Test Name',
            'first_filled_at' => now()->format('Y-m-d'),
            'first_filled_age' => 20,
            'first_filled_education' => 'SMA',
            'profile_photo' => $file,
        ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);
    }

    public function test_validation_errors()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($user)->post(route('rmd.profile.store'), [
            'first_name' => '', // Required
        ]);

        $response->assertSessionHasErrors('first_name');
    }
}
