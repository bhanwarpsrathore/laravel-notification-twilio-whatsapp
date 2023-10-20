<?php

namespace TwilioWhatsApp\Tests\Unit\Channels;

use TwilioWhatsApp\Tests\Unit\Notifiable;
use TwilioWhatsApp\Tests\Unit\NotifiableWithMethod;

use Illuminate\Notifications\Notification;

use Twilio\Exceptions\RestException;

use TwilioWhatsApp\TwilioWhatsApp;
use TwilioWhatsApp\TwilioWhatsAppConfig;
use TwilioWhatsApp\Messages\WhatsAppMessage;
use TwilioWhatsApp\Channels\TwilioWhatsAppChannel;
use TwilioWhatsApp\Exceptions\CouldNotSendNotification;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class TwilioWhatsAppChannelTest extends MockeryTestCase {

    /** @var TwilioWhatsAppChannel */
    protected $channel;

    /** @var TwilioWhatsApp */
    protected $twilio;

    public function setUp(): void {
        parent::setUp();

        $this->twilio = Mockery::mock(TwilioWhatsApp::class);
        $this->twilio->config = new TwilioWhatsAppConfig([
            'ignored_error_codes' => []
        ]);

        $this->channel = new TwilioWhatsAppChannel($this->twilio);
    }

    /** @test */
    public function it_will_not_send_a_message_without_known_receiver() {
        $notifiable = new Notifiable();
        $notification = Mockery::mock(Notification::class);

        $result = $this->channel->send($notifiable, $notification);
        $this->assertNull($result);
    }

    /** @test */
    public function it_will_send_a_sms_message_to_the_result_of_the_route_method_of_the_notifiable() {
        $notifiable = new NotifiableWithMethod();
        $notification = Mockery::mock(Notification::class);

        $message = new WhatsAppMessage('Message text');
        $notification->shouldReceive('toTwilioWhatsApp')->andReturn($message);

        $this->twilio->shouldReceive('sendMessage')
            ->atLeast()->once()
            ->with($message, '+1111111111');

        $this->channel->send($notifiable, $notification);
    }

    /** @test */
    public function it_will_convert_a_string_to_a_sms_message() {
        $notifiable = new NotifiableWithMethod();
        $notification = Mockery::mock(Notification::class);

        $notification->shouldReceive('toTwilioWhatsApp')->andReturn('Message text');

        $this->twilio->shouldReceive('sendMessage')
            ->atLeast()->once()
            ->with(Mockery::type(WhatsAppMessage::class), Mockery::any());

        $this->channel->send($notifiable, $notification);
    }

    /** @test */
    public function it_will_fire_an_event_in_case_of_an_invalid_message() {
        $notifiable = new NotifiableWithMethod();
        $notification = Mockery::mock(Notification::class);

        // Invalid message
        $notification->shouldReceive('toTwilioWhatsApp')->andReturn(-1);

        $this->expectException(CouldNotSendNotification::class);
        $this->channel->send($notifiable, $notification);
    }

    /** 
     * @test
     * @doesNotPerformAssertions
     * */
    public function it_will_ignore_specific_error_codes() {
        $notifiable = new NotifiableWithMethod();
        $notification = Mockery::mock(Notification::class);

        $this->twilio->config = new TwilioWhatsAppConfig([
            'ignored_error_codes' => [
                44444
            ],
        ]);

        $notification->shouldReceive('toTwilioWhatsApp')->andReturn('Message text');

        $this->twilio->shouldReceive('sendMessage')
            ->andThrow(new RestException('error', 44444, 400));

        $result = $this->channel->send($notifiable, $notification);
    }

    /** 
     * @test
     * @doesNotPerformAssertions
     * */
    public function it_will_ignore_all_error_codes() {
        $notifiable = new NotifiableWithMethod();
        $notification = Mockery::mock(Notification::class);

        $this->twilio->config = new TwilioWhatsAppConfig([
            'ignored_error_codes' => ['*'],
        ]);

        $notification->shouldReceive('toTwilioWhatsApp')->andReturn('Message text');

        $this->twilio->shouldReceive('sendMessage')
            ->andThrow(new RestException('error', 44444, 400));

        $this->channel->send($notifiable, $notification);
    }

    /** @test */
    public function it_will_rethrow_non_ignored_error_codes() {
        $notifiable = new NotifiableWithMethod();
        $notification = Mockery::mock(Notification::class);

        $this->twilio->config = new TwilioWhatsAppConfig([
            'ignored_error_codes' => [
                55555,
            ],
        ]);

        $notification->shouldReceive('toTwilioWhatsApp')->andReturn('Message text');

        $this->twilio->shouldReceive('sendMessage')
            ->andThrow(new RestException('error', 44444, 400));

        $this->expectException(RestException::class);
        $this->channel->send($notifiable, $notification);
    }
}
