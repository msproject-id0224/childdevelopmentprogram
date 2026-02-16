<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed_for_participant()
    {
        $user = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($user)
            ->get('/profile');

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profile/Edit')
                ->has('auth.user')
                ->has('mustVerifyEmail')
                ->has('status')
            );
    }

    public function test_profile_page_is_displayed_for_mentor()
    {
        $user = User::factory()->create(['role' => 'mentor']);

        $response = $this->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_participant_can_upload_photo_request_from_profile_page()
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'participant']);
        $file = UploadedFile::fake()->create('avatar.jpg');

        $response = $this->actingAs($user)
            ->post(route('participant.profile-photo.request'), [
                'photo' => $file,
            ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('pending', $user->profile_photo_status);
    }

    public function test_mentor_can_upload_photo_request_from_profile_page()
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'mentor']);
        $file = UploadedFile::fake()->create('avatar.jpg');

        $response = $this->actingAs($user)
            ->post(route('mentor.profile-photo.request'), [
                'photo' => $file,
            ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('pending', $user->profile_photo_status);
    }

    public function test_admin_cannot_upload_photo_request()
    {
        // Admin usually shouldn't see the form, but if they try to hit the route:
        // Admin route for direct upload exists, but request route is for participants/mentors.
        // Actually admins might not have a request route.
        // Let's check web.php
        // 'mentor.profile-photo.request' is middleware 'role:admin,mentor'. So admin CAN use it?
        // 'participant.profile-photo.request' is 'role:participant'. Admin CANNOT.
        
        $admin = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->create('avatar.jpg');

        // Try participant route
        $response = $this->actingAs($admin)
            ->post(route('participant.profile-photo.request'), [
                'photo' => $file,
            ]);
        $response->assertForbidden(); // Should be 403
    }
}
