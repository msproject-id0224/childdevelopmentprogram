<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Otp;
use App\Mail\OtpMail;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Tests\TestCase;

class AgeValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_fails_for_underage_participants(): void
    {
        Mail::fake();

        $minAge = User::MINIMUM_PARTICIPANT_AGE;
        $underageDob = Carbon::now()->subYears($minAge - 1)->format('Y-m-d');

        $response = $this->post('/register', [
            'first_name' => 'Underage',
            'last_name' => 'User',
            'id_number' => 'U12345',
            'email' => 'underage@example.com',
            'date_of_birth' => $underageDob,
        ]);

        $response->assertSessionHasErrors('date_of_birth');
        $this->assertDatabaseMissing('users', ['email' => 'underage@example.com']);
    }

    public function test_registration_succeeds_for_eligible_participants(): void
    {
        Mail::fake();

        $minAge = User::MINIMUM_PARTICIPANT_AGE;
        $eligibleDob = Carbon::now()->subYears($minAge)->format('Y-m-d');

        $response = $this->post('/register', [
            'first_name' => 'Eligible',
            'last_name' => 'User',
            'id_number' => 'E12345',
            'email' => 'eligible@example.com',
            'date_of_birth' => $eligibleDob,
        ]);

        $response->assertRedirect(route('otp.view'));
        $this->assertDatabaseHas('users', [
            'email' => 'eligible@example.com',
            'role' => User::ROLE_PARTICIPANT,
        ]);
    }

    public function test_login_blocked_for_participants_who_became_underage_manually(): void
    {
        Mail::fake();

        // Create a participant who is underage (maybe data was changed or imported)
        $minAge = User::MINIMUM_PARTICIPANT_AGE;
        $underageUser = User::factory()->create([
            'role' => User::ROLE_PARTICIPANT,
            'date_of_birth' => Carbon::now()->subYears($minAge - 1),
            'email' => 'blocked@example.com',
            'is_active' => true,
        ]);

        // Mock OTP
        $otpCode = '123456';
        Otp::create([
            'email' => $underageUser->email,
            'code' => Hash::make($otpCode),
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $response = $this->withSession(['email' => $underageUser->email])
                         ->post(route('otp.verify'), [
                             'otp' => $otpCode,
                         ]);

        $response->assertSessionHasErrors('otp');
        $this->assertGuest();

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $underageUser->id,
            'action' => 'LOGIN_BLOCKED_UNDERAGE',
        ]);
    }

    public function test_mentors_and_admins_exempt_from_age_restriction(): void
    {
        Mail::fake();

        // Create a mentor who is technically "underage" if they were a participant
        $underageMentor = User::factory()->create([
            'role' => User::ROLE_MENTOR,
            'date_of_birth' => Carbon::now()->subYears(10), // 10 years old
            'email' => 'mentor@example.com',
            'is_active' => true,
        ]);

        $otpCode = '123456';
        Otp::create([
            'email' => $underageMentor->email,
            'code' => Hash::make($otpCode),
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $response = $this->withSession(['email' => $underageMentor->email])
                         ->post(route('otp.verify'), [
                             'otp' => $otpCode,
                         ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($underageMentor);
    }
}
