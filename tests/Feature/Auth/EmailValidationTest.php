<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;

class EmailValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that emails are normalized (trimmed and lowercased) when saving User.
     */
    public function test_email_normalization_on_user_create()
    {
        $email = '  Test.User@Example.COM  ';
        $user = User::factory()->create(['email' => $email]);

        $this->assertEquals('test.user@example.com', $user->email);
        $this->assertDatabaseHas('users', ['email' => 'test.user@example.com']);
    }

    /**
     * Test that login request validates email format strictly.
     */
    public function test_login_request_validates_email_format()
    {
        $invalidEmails = [
            'plainaddress',
            '#@%^%#$@#$@#.com',
            '@example.com',
            'Joe Smith <email@example.com>',
            'email.example.com',
            'email@example@example.com',
            '.email@example.com',
            'email.@example.com',
            'email..email@example.com',
        ];

        foreach ($invalidEmails as $email) {
            $response = $this->postJson('/login', ['email' => $email]);
            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['email']);
        }
    }

    /**
     * Test that login input is normalized before validation/processing.
     * If user types "  test@example.com  ", it should work if user exists as "test@example.com".
     */
    public function test_login_input_is_normalized()
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'valid@example.com']);

        // Input with spaces and caps
        $response = $this->postJson('/login', ['email' => '  Valid@Example.COM  ']);

        $response->assertStatus(200);
        $response->assertSessionHas('email', 'valid@example.com');
    }

    /**
     * Test that OTP service handles email sending failure gracefully.
     */
    public function test_otp_service_handles_mail_failure()
    {
        Mail::shouldReceive('to')->andThrow(new \Exception('Mail server down'));
        
        $user = User::factory()->create(['email' => 'valid@example.com']);

        $response = $this->postJson('/login', ['email' => $user->email]);

        $response->assertStatus(422); // Validation error response
        $response->assertJsonValidationErrors(['email']);
        $this->assertStringContainsString('Gagal mengirim email OTP', $response->json()['errors']['email'][0]);
    }
}
