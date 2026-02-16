<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Otp;
use App\Mail\OtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_receive_otp_upon_registration(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'id_number' => '123456789',
            'email' => 'test@example.com',
        ]);

        // User should be created
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'id_number' => '123456789',
        ]);

        // Should NOT be authenticated yet
        $this->assertGuest();

        // Should receive OTP email
        Mail::assertSent(OtpMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });

        // OTP should be in database
        $this->assertDatabaseHas('otps', [
            'email' => 'test@example.com',
        ]);

        // Should redirect to OTP verification page
        $response->assertRedirect(route('otp.view'));
        
        // Session should contain email and success message
        $response->assertSessionHas('email', 'test@example.com');
        $response->assertSessionHas('success', 'Kode OTP berhasil dikirim ke email Anda.');
    }

    public function test_user_can_verify_registration_otp_and_login(): void
    {
        Mail::fake();

        // 1. Register
        $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'id_number' => '987654321',
            'email' => 'verify@example.com',
        ]);

        // Capture OTP from Mail
        $otpCode = null;
        Mail::assertSent(OtpMail::class, function ($mail) use (&$otpCode) {
            if ($mail->hasTo('verify@example.com')) {
                $otpCode = $mail->code;
                return true;
            }
            return false;
        });
        
        // 2. Verify OTP
        $response = $this->withSession(['email' => 'verify@example.com'])
                         ->post(route('otp.verify'), [
                             'otp' => $otpCode,
                         ]);

        // 3. Should be authenticated and redirected to dashboard
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_fails_with_invalid_data(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'first_name' => '', // Invalid
            'email' => 'invalid-email', // Invalid
        ]);

        $response->assertSessionHasErrors(['first_name', 'email']);
        Mail::assertNothingSent();
    }
}
