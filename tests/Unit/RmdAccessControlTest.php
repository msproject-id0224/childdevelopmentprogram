<?php

namespace Tests\Unit;

use App\Http\Middleware\RmdAccessControl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RmdAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_allows_participants(): void
    {
        $user = User::factory()->make(['role' => User::ROLE_PARTICIPANT]);
        Auth::shouldReceive('user')->andReturn($user);

        $middleware = new RmdAccessControl();
        $request = Request::create('/rmd', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return new \Illuminate\Http\Response('allowed');
        });

        $this->assertEquals('allowed', $response->getContent());
    }

    public function test_it_denies_admins_and_logs_attempt(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Auth::shouldReceive('user')->andReturn($user);

        $middleware = new RmdAccessControl();
        $request = Request::create('/rmd', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Unauthorized. RMD module is restricted to participants only.');

        try {
            $middleware->handle($request, function ($req) {});
        } catch (HttpException $e) {
            $this->assertDatabaseHas('audit_logs', [
                'user_id' => $user->id,
                'action' => 'UNAUTHORIZED_ACCESS_ATTEMPT',
                'target_type' => 'RMD_MODULE',
            ]);
            throw $e;
        }
    }

    public function test_it_denies_mentors_and_logs_attempt(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MENTOR]);
        Auth::shouldReceive('user')->andReturn($user);

        $middleware = new RmdAccessControl();
        $request = Request::create('/rmd', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Unauthorized. RMD module is restricted to participants only.');

        try {
            $middleware->handle($request, function ($req) {});
        } catch (HttpException $e) {
            $this->assertDatabaseHas('audit_logs', [
                'user_id' => $user->id,
                'action' => 'UNAUTHORIZED_ACCESS_ATTEMPT',
                'target_type' => 'RMD_MODULE',
            ]);
            throw $e;
        }
    }
}
