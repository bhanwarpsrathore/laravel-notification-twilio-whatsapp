<?php

namespace TwilioWhatsApp;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client as TwilioService;

use TwilioWhatsApp\Channels\TwilioWhatsAppChannel;
use TwilioWhatsApp\Exceptions\InvalidConfigException;

class TwilioWhatsAppProvider extends ServiceProvider implements DeferrableProvider {

    /**
     * Bootstrap the application services.
     */
    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/twilio-whatsapp-notification.php' => $this->app->configPath('twilio-whatsapp-notification.php'),
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register() {
        $this->mergeConfigFrom(__DIR__ . '/../config/twilio-whatsapp-notification.php', 'twilio-whatsapp-notification');

        $this->app->bind(TwilioWhatsAppConfig::class, function () {
            return new TwilioWhatsAppConfig($this->app['config']['twilio-whatsapp-notification']);
        });

        $this->app->singleton(TwilioService::class, function (Application $app) {
            /** @var TwilioWhatsAppConfig $config */
            $config = $app->make(TwilioWhatsAppConfig::class);

            if ($config->usingUsernamePasswordAuth()) {
                return new TwilioService($config->getUsername(), $config->getPassword(), $config->getAccountSid());
            }

            if ($config->usingTokenAuth()) {
                return new TwilioService($config->getAccountSid(), $config->getAuthToken());
            }

            throw InvalidConfigException::missingConfig();
        });

        $this->app->singleton(TwilioWhatsApp::class, function (Application $app) {
            return new TwilioWhatsApp(
                $app->make(TwilioService::class),
                $app->make(TwilioWhatsAppConfig::class)
            );
        });

        $this->app->singleton(TwilioWhatsAppChannel::class, function (Application $app) {
            return new TwilioWhatsAppChannel(
                $app->make(TwilioWhatsApp::class)
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array {
        return [
            TwilioService::class,
            TwilioWhatsAppConfig::class,
            TwilioWhatsAppChannel::class,
        ];
    }
}
