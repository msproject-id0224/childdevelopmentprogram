<?php

namespace Tests\Feature\Channels;

use App\Channels\TwilioChannel;
use Illuminate\Notifications\Notification;
use Mockery;
use PHPUnit\Framework\TestCase;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class TwilioChannelTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_sends_notification_via_twilio()
    {
        // Mock Notifiable
        $notifiable = Mockery::mock();
        $notifiable->shouldReceive('routeNotificationFor')->with('twilio', Mockery::any())->andReturn('+628123456789');

        // Mock Notification
        $notification = Mockery::mock(Notification::class);
        $notification->shouldReceive('toTwilio')->with($notifiable)->andReturn([
            'content' => 'Test message',
            'whatsapp' => false,
        ]);

        // Mock Twilio Client
        $client = Mockery::mock(Client::class);
        $messages = Mockery::mock();
        $client->messages = $messages;
        
        $response = (object) ['sid' => 'SM123'];
        $messages->shouldReceive('create')->with('+628123456789', [
            'from' => 'TWILIO_FROM',
            'body' => 'Test message'
        ])->andReturn($response);

        // Mock Config
        // Note: Since this is a Unit test and we're not extending Laravel's TestCase, 
        // we can't easily use Config facade without loading the whole framework.
        // Instead, I'll rely on the TwilioChannel constructor if I were to inject config, 
        // but since I'm using config() inside the method, I'll switch to a Feature test 
        // or mock the config facade if possible. 
        // Actually, let's just make it a Feature test to have access to Laravel's goodies.
    }
}
