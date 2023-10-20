<?php

namespace TwilioWhatsApp\Tests\Unit\Messages;

use TwilioWhatsApp\Messages\WhatsAppMedia;

class WhatsAppMediaTest extends WhatsAppMessageTest {

    public function setUp(): void {
        parent::setUp();

        $this->message = new WhatsAppMedia();
    }

    /** @test */
    public function it_sets_media_url() {
        $mediaUrl = "https://picsum.photos/300";
        $this->message->mediaUrl($mediaUrl);

        $this->assertEquals($mediaUrl, $this->message->mediaUrl);
    }
}
