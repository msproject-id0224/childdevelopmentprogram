<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAddMentorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_new_mentor_with_comprehensive_details(): void
    {
        // 1. Authenticate as Admin
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        // 2. Visit Create Page (Verification of route and access)
        $response = $this->get(route('mentors.create'));
        $response->assertStatus(200);

        // 3. Prepare Mentor Data
        $mentorData = [
            'first_name' => 'Jelny',
            'last_name' => 'Karinda',
            'nickname' => 'Jel',
            'email' => 'jelny.karinda@example.com',
            'role' => User::ROLE_MENTOR,
            'phone_number' => '081234567890',
            'specialization' => 'Child Psychology',
            'experience' => '5 Years Experience',
            'bio' => 'Experienced mentor in child development and psychology.',
            'date_of_birth' => '1990-01-01',
            'age' => 34,
            'gender' => 'female',
            'age_group' => 'Survival', // Based on dropdown options
        ];

        // 4. Submit Data
        $response = $this->post(route('mentors.store'), $mentorData);

        // 5. Assert Success Redirect
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Mentor created successfully.');

        // 6. Assert Database Storage
        $this->assertDatabaseHas('users', [
            'first_name' => 'Jelny',
            'last_name' => 'Karinda',
            'email' => 'jelny.karinda@example.com',
            'role' => User::ROLE_MENTOR,
            'phone_number' => '081234567890',
            'specialization' => 'Child Psychology',
            'experience' => '5 Years Experience',
            'bio' => 'Experienced mentor in child development and psychology.',
            'gender' => 'female',
            'is_active' => true,
        ]);

        // 7. Verify visibility in Mentor List
        $response = $this->get(route('mentors.index'));
        $response->assertStatus(200);
        $response->assertSee('Jelny');
        $response->assertSee('Karinda');
    }

    public function test_admin_add_mentor_validation_errors(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        // Test missing required fields
        $response = $this->post(route('mentors.store'), []);
        
        $response->assertSessionHasErrors(['first_name', 'email', 'role']);
    }

    public function test_admin_can_add_new_participant_via_mentor_form(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $participantData = [
            'first_name' => 'New',
            'last_name' => 'Participant',
            'email' => 'participant.new@example.com',
            'role' => User::ROLE_PARTICIPANT,
            'age_group' => '0-2',
        ];

        $response = $this->post(route('mentors.store'), $participantData);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'participant.new@example.com',
            'role' => User::ROLE_PARTICIPANT,
        ]);
    }

    public function test_admin_can_add_new_admin_via_mentor_form(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $adminData = [
            'first_name' => 'New',
            'last_name' => 'Admin',
            'email' => 'admin.new@example.com',
            'role' => User::ROLE_ADMIN,
        ];

        $response = $this->post(route('mentors.store'), $adminData);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'admin.new@example.com',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_admin_is_visible_in_mentor_list(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);
        
        // Create another admin
        $otherAdmin = User::factory()->create([
            'first_name' => 'Other',
            'last_name' => 'Admin',
            'email' => 'other.admin@example.com',
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->get(route('mentors.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Other');
        $response->assertSee('Admin');
        $response->assertSee('other.admin@example.com');
        $response->assertSee('admin'); // Role value in props
    }
}
