<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminFirstLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_adminfirst_user_can_login_successfully()
    {
        // 1. Setup user adminfirst@cdp.com
        $email = 'adminfirst@cdp.com';
        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'First',
            'email' => $email,
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'id_number' => 'ADM001',
        ]);

        // 2. Mock OTP verification
        $plainOtp = '654321';
        Otp::create([
            'email' => $email,
            'code' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes(5),
        ]);

        // 3. Attempt verification
        $response = $this->withSession(['email' => $email])
                         ->post(route('otp.verify'), [
                             'otp' => $plainOtp,
                         ]);

        // 4. Assertions
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseMissing('otps', ['email' => $email]);
    }

    public function test_adminfirst_user_cannot_login_if_inactive()
    {
        // 1. Setup inactive user
        $email = 'adminfirst@cdp.com';
        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'First',
            'email' => $email,
            'role' => User::ROLE_ADMIN,
            'is_active' => false,
            'id_number' => 'ADM001',
        ]);

        $plainOtp = '654321';
        Otp::create([
            'email' => $email,
            'code' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes(5),
        ]);

        // 2. Attempt verification
        $response = $this->withSession(['email' => $email])
                         ->post(route('otp.verify'), [
                             'otp' => $plainOtp,
                         ]);

        // 3. Assertions
        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Akun Anda tidak aktif. Silakan hubungi admin.']);
    }
}
