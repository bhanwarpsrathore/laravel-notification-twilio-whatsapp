<?php

namespace TwilioWhatsApp\Tests\Unit;

use Twilio\Rest\Client as TwilioService;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Api\V2010\Account\MessageInstance;

use TwilioWhatsApp\TwilioWhatsApp;
use TwilioWhatsApp\TwilioWhatsAppConfig;
use TwilioWhatsApp\Messages\WhatsAppMessage;
use TwilioWhatsApp\Messages\WhatsAppMedia;
use TwilioWhatsApp\Exceptions\CouldNotSendNotification;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class TwilioWhatsAppTest extends MockeryTestCase {

    /** @var TwilioWhatsApp */
    protected $twilio;

    /** @var TwilioService */
    protected $twilioService;

    /** @var TwilioWhatsAppConfig */
    protected $config;

    public function setUp(): void {
        parent::setUp();

        $this->twilioService = Mockery::mock(TwilioService::class);
        $this->twilioService->messages = Mockery::mock(MessageList::class);

        $this->config = Mockery::mock(TwilioWhatsAppConfig::class);
        $this->twilio = new TwilioWhatsApp($this->twilioService, $this->config);
    }

    /**
     * @test
     */
    public function it_sends_a_text_message_to_twilio() {
        $content = 'Message Text';
        $message = new WhatsAppMessage($content);
        $message->statusCallback('http://example.com');
        $message->statusCallbackMethod('PUT');

        $this->config->shouldReceive('getFrom')
            ->once()
            ->andReturn('+1234567890');

        $this->twilioService->messages->shouldReceive('create')
            ->atLeast()->once()
            ->with('whatsapp:+1111111111', [
                'from' => 'whatsapp:+1234567890',
                'body' => $content,
                'statusCallback' => 'http://example.com',
                'statusCallbackMethod' => 'PUT'
            ])
            ->andReturn(Mockery::mock(MessageInstance::class));

        $this->twilio->sendMessage($message, '+1111111111');
    }

    /**
     * @test
     */
    public function it_sends_a_media_message_to_twilio() {
        $content = 'Message Text';
        $message = new WhatsAppMedia($content);
        $message->mediaUrl('http://example.com');
        $message->statusCallback('http://example.com');
        $message->statusCallbackMethod('PUT');

        $this->config->shouldReceive('getFrom')
            ->once()
            ->andReturn('+1234567890');

        $this->twilioService->messages->shouldReceive('create')
            ->atLeast()->once()
            ->with('whatsapp:+1111111111', [
                'from' => 'whatsapp:+1234567890',
                'body' => $content,
                'mediaUrl' => 'http://example.com',
                'statusCallback' => 'http://example.com',
                'statusCallbackMethod' => 'PUT'
            ])
            ->andReturn(Mockery::mock(MessageInstance::class));

        $this->twilio->sendMessage($message, '+1111111111');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_missing_from_number() {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Notification was not sent. Missing `from` number.');

        $content = 'Message Text';
        $message = new WhatsAppMessage($content);

        $this->config->shouldReceive('getFrom')
            ->once()
            ->andReturn(null);

        $this->twilio->sendMessage($message, '+1111111111');
    }
}
