<?php

declare(strict_types=1);

namespace TwilioWhatsApp\Tests\Integration;

use TwilioWhatsApp\Exceptions\InvalidConfigException;
use TwilioWhatsApp\Channels\TwilioWhatsAppChannel;

class TwilioWhatsAppProviderTest extends BaseIntegrationTestCase {

    public function testThatApplicationCannotCreateChannelWithoutConfig() {
        $this->expectException(InvalidConfigException::class);

        $this->app->get(TwilioWhatsAppChannel::class);
    }

    public function testThatApplicationCannotCreateChannelWithoutSid() {
        $this->app['config']->set('twilio-whatsapp-notification.username', 'test');
        $this->app['config']->set('twilio-whatsapp-notification.password', 'password');

        $this->expectException(InvalidConfigException::class);
        $this->app->get(TwilioWhatsAppChannel::class);
    }

    public function testThatApplicationCreatesChannelWithConfig() {
        $this->app['config']->set('twilio-whatsapp-notification.username', 'test');
        $this->app['config']->set('twilio-whatsapp-notification.password', 'password');
        $this->app['config']->set('twilio-whatsapp-notification.account_sid', '1234');

        $this->assertInstanceOf(TwilioWhatsAppChannel::class, $this->app->get(TwilioWhatsAppChannel::class));
    }
}
