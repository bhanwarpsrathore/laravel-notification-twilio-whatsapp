<?php

namespace TwilioWhatsApp\Tests\Unit\Messages;

use TwilioWhatsApp\Messages\WhatsAppMessage;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class WhatsAppMessageTest extends MockeryTestCase {

    /** @var WhatsAppMessage */
    protected $message;

    public function setUp(): void {
        parent::setUp();

        $this->message = new WhatsAppMessage();
    }

    /** @test */
    public function it_can_accept_content_when_constructing_a_message() {
        $content = "Message Text";
        $instance = new WhatsAppMessage($content);

        $this->assertEquals($content, $instance->content);
    }

    /** @test */
    public function it_provides_a_create_method() {
        $instance = WhatsAppMessage::create();

        $this->assertInstanceOf(WhatsAppMessage::class, $instance);
    }

    /** @test */
    public function it_can_set_the_content() {
        $content = "Message Text";
        $this->message->content($content);
        $this->assertEquals($content, $this->message->content);
    }

    /** @test */
    public function it_can_set_the_from() {
        $from = "+1234567890";
        $this->message->from($from);
        $this->assertEquals($from, $this->message->from);
    }

    /** @test */
    public function it_can_return_the_from_using_getter() {
        $from = "+1234567890";
        $this->message->from($from);
        $this->assertEquals($from, $this->message->getFrom());
    }
}
