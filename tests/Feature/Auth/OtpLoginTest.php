<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Otp;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OtpLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear rate limiter
        \Illuminate\Support\Facades\RateLimiter::clear('otp-verify-ip:127.0.0.1');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_request_otp_with_email(): void
    {
        Notification::fake();
        // Use a real domain for DNS check
        $user = User::factory()->create(['email' => 'test@gmail.com', 'is_active' => true]);

        $response = $this->postJson('/login', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'nextScreen' => 'otp',
                 ]);
        
        $response->assertSessionHas('email', $user->email);
        
        Notification::assertSentTo(
            [$user],
            OtpNotification::class
        );
    }

    public function test_users_can_verify_otp_and_login(): void
    {
        Notification::fake();
        $user = User::factory()->create([
            'email' => 'verify@gmail.com', 
            'is_active' => true,
            'date_of_birth' => Carbon::now()->subYears(20), // Ensure user is old enough
        ]);

        // 1. Setup OTP
        $plainOtp = '123456';
        Otp::create([
            'email' => $user->email,
            'code' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes(5),
            'status' => 'pending', // Explicitly set status
            'created_at' => now(),
        ]);

        // 2. Verify OTP via session (Web flow)
        $response = $this->withSession(['email' => $user->email])
                         ->post(route('otp.verify'), [
                             'otp' => $plainOtp,
                         ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
        
        // Verify OTP is marked as used
        $this->assertDatabaseHas('otps', [
            'email' => $user->email,
            'status' => 'used'
        ]);
        
        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'LOGIN_SUCCESS',
            'details' => json_encode(['method' => 'OTP']),
        ]);
    }

    public function test_api_users_can_verify_otp_and_receive_json(): void
    {
        $user = User::factory()->create([
            'email' => 'api@gmail.com', 
            'is_active' => true,
            'date_of_birth' => Carbon::now()->subYears(20), // Ensure user is old enough
        ]);
        
        $plainOtp = '123456';
        
        Otp::create([
            'email' => $user->email,
            'code' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes(5),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $response = $this->postJson(route('otp.verify'), [
            'email' => $user->email, // API requires email explicitly if not in session
            'otp' => $plainOtp,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'timestamp',
                     'user',
                     'redirect_url',
                 ]);
                 
        $this->assertAuthenticatedAs($user);
    }

    public function test_rate_limiting_blocks_brute_force_attempts(): void
    {
        $user = User::factory()->create();
        
        // Make 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->withSession(['email' => $user->email])
                 ->post(route('otp.verify'), ['otp' => '000000']);
        }

        // 6th attempt should be blocked even with correct OTP
        $plainOtp = '123456';
        Otp::create([
            'email' => $user->email,
            'code' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->withSession(['email' => $user->email])
                         ->post(route('otp.verify'), ['otp' => $plainOtp]);

        $response->assertSessionHasErrors('otp'); // "Terlalu banyak percobaan..."
        $this->assertGuest();
    }

    public function test_underage_participant_cannot_login(): void
    {
        // Create underage participant (10 years old)
        $dob = Carbon::now()->subYears(10);
        $user = User::factory()->create([
            'role' => 'participant',
            'date_of_birth' => $dob,
        ]);

        $plainOtp = '123456';
        Otp::create([
            'email' => $user->email,
            'code' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->withSession(['email' => $user->email])
                         ->post(route('otp.verify'), ['otp' => $plainOtp]);

        $this->assertGuest();
        $response->assertSessionHasErrors('otp'); // "Maaf, usia Anda belum memenuhi syarat..."
        
        // Check Audit Log for blocking
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'LOGIN_BLOCKED_UNDERAGE',
        ]);
    }

    public function test_expired_otp_is_rejected(): void
    {
        $user = User::factory()->create();
        $plainOtp = '123456';
        
        Otp::create([
            'email' => $user->email,
            'code' => Hash::make($plainOtp),
            'expires_at' => now()->subMinutes(1), // Expired
        ]);

        $response = $this->withSession(['email' => $user->email])
                         ->post(route('otp.verify'), ['otp' => $plainOtp]);

        $this->assertGuest();
        $response->assertSessionHasErrors('otp');
    }
}
