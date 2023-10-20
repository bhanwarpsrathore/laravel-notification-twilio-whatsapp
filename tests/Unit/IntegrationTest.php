<?php

namespace TwilioWhatsApp\Tests\Unit;

use Illuminate\Notifications\Notification;

use Twilio\Rest\Client as TwilioService;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Api\V2010\Account\MessageInstance;

use TwilioWhatsApp\TwilioWhatsApp;
use TwilioWhatsApp\TwilioWhatsAppConfig;
use TwilioWhatsApp\Messages\WhatsAppMessage;
use TwilioWhatsApp\Channels\TwilioWhatsAppChannel;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class IntegrationTest extends MockeryTestCase {

    /** @var TwilioService */
    protected $twilioService;

    /** @var Notification */
    protected $notification;

    public function setUp(): void {
        parent::setUp();

        $this->twilioService = Mockery::mock(TwilioService::class);
        $this->twilioService->messages = Mockery::mock(MessageList::class);

        $this->notification = Mockery::mock(Notification::class);
    }

    /** @test */
    public function it_can_send_a_text_message() {
        $message = WhatsAppMessage::create('Message text');
        $this->notification->shouldReceive('toTwilioWhatsApp')->andReturn($message);

        $config = new TwilioWhatsAppConfig([
            'from' => '+9876543210',
        ]);


        $twilio = new TwilioWhatsApp($this->twilioService, $config);
        $channel = new TwilioWhatsAppChannel($twilio);

        $this->messageWillBeSentToTwilioWith('whatsapp:+1111111111', [
            'body' => 'Message text',
            'from' => 'whatsapp:+9876543210'
        ]);
        $channel->send(new NotifiableWithMethod(), $this->notification);
    }

    protected function messageWillBeSentToTwilioWith(...$args) {
        $this->twilioService->messages->shouldReceive('create')
            ->atLeast()->once()
            ->with(...$args)
            ->andReturn(Mockery::mock(MessageInstance::class));
    }
}
