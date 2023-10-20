<?php

declare(strict_types=1);

namespace TwilioWhatsApp\Tests\Integration;

use Orchestra\Testbench\TestCase;

use TwilioWhatsApp\TwilioWhatsAppProvider;

abstract class BaseIntegrationTestCase extends TestCase {

    protected function getPackageProviders($app) {
        return [TwilioWhatsAppProvider::class];
    }
}
