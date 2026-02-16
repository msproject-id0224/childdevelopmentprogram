<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notifiable;

class EmailQueueTest extends TestCase
{
    /**
     * Test that OTP notification is queueable.
     */
    public function test_otp_notification_implements_should_queue()
    {
        $notification = new OtpNotification('123456');
        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    /**
     * Test that OTP notification is dispatched to queue.
     */
    public function test_otp_notification_is_queued()
    {
        Notification::fake();

        // Create a generic notifiable object
        $user = new class {
            use Notifiable;
            public $email = 'test@example.com';
            public function getKey() { return 1; }
        };
        
        $user->notify(new OtpNotification('123456'));

        Notification::assertSentTo(
            $user,
            OtpNotification::class,
            function ($notification, $channels) {
                // Check if 'mail' is in the channels
                return in_array('mail', $channels) && $notification->otp === '123456';
            }
        );
    }
}
