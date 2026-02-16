<?php

namespace Tests\Feature\Auth;

use App\Models\AuditLog;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OtpNotification;
use Tests\TestCase;

class LoginFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup user specific for this test scenario
        $this->user = User::factory()->create([
            'email' => 'mawarsaronp@gmail.com',
            'is_active' => true,
            'role' => 'participant',
            'date_of_birth' => now()->subYears(20), // Ensure age requirement is met
        ]);
    }

    public function test_1_validate_email_existence_and_otp_generation()
    {
        Notification::fake();

        // Case 1.1: Valid Email
        $response = $this->post('/login', [
            'email' => 'mawarsaronp@gmail.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        // Verify OTP was stored in DB
        $this->assertDatabaseHas('otps', [
            'email' => 'mawarsaronp@gmail.com',
        ]);

        // Verify Notification was sent
        Notification::assertSentTo(
            $this->user,
            OtpNotification::class
        );

        // Case 1.2: Invalid Email (Not registered)
        $response = $this->post('/login', [
            'email' => 'unregistered@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_2_authenticate_with_otp()
    {
        // Setup OTP
        $otpCode = '123456';
        Otp::create([
            'email' => 'mawarsaronp@gmail.com',
            'code' => Hash::make($otpCode),
            'expires_at' => now()->addMinutes(5),
        ]);

        // Simulate session
        session(['email' => 'mawarsaronp@gmail.com']);

        // Case 2.1: Successful Login
        $response = $this->post('/verify-otp', [
            'otp' => $otpCode,
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($this->user);
        
        // Verify OTP is deleted after use
        $this->assertDatabaseMissing('otps', [
            'email' => 'mawarsaronp@gmail.com',
        ]);
    }

    public function test_3_error_handling_scenarios()
    {
        // Setup
        session(['email' => 'mawarsaronp@gmail.com']);

        // Case 3.1: Invalid OTP (Password salah/OTP salah)
        $otpCode = '123456';
        Otp::create([
            'email' => 'mawarsaronp@gmail.com',
            'code' => Hash::make($otpCode),
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->post('/verify-otp', [
            'otp' => '000000', // Wrong code
        ]);

        $response->assertSessionHasErrors(['otp' => 'Kode OTP salah.']);
        $this->assertGuest();

        // Case 3.2: Account Inactive
        $this->user->update(['is_active' => false]);
        
        $response = $this->post('/verify-otp', [
            'otp' => $otpCode,
        ]);

        $response->assertSessionHasErrors(['email' => 'Akun Anda tidak aktif. Silakan hubungi admin.']);
        $this->assertGuest();

        // Reset active status
        $this->user->update(['is_active' => true]);

        // Case 3.3: OTP Expired
        $expiredOtp = Otp::where('email', 'mawarsaronp@gmail.com')->first();
        $expiredOtp->update(['expires_at' => now()->subMinute()]);

        $response = $this->post('/verify-otp', [
            'otp' => $otpCode,
        ]);

        $response->assertSessionHasErrors(['otp' => 'Kode OTP telah kadaluarsa. Silakan minta OTP baru.']);
        $this->assertGuest();
    }

    public function test_6_audit_log_recording()
    {
        // Setup OTP
        $otpCode = '123456';
        Otp::create([
            'email' => 'mawarsaronp@gmail.com',
            'code' => Hash::make($otpCode),
            'expires_at' => now()->addMinutes(5),
        ]);

        session(['email' => 'mawarsaronp@gmail.com']);

        // Perform login
        $this->post('/verify-otp', [
            'otp' => $otpCode,
        ]);

        // Check Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->user->id,
            'action' => 'LOGIN_SUCCESS',
        ]);
    }
}
