<?php

namespace TwilioWhatsApp;

use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client as TwilioService;

use TwilioWhatsApp\Exceptions\CouldNotSendNotification;
use TwilioWhatsApp\Messages\WhatsAppMessage;
use TwilioWhatsApp\Messages\WhatsAppMedia;

class TwilioWhatsApp {

    /** @var TwilioService */
    protected $twilioService;

    /** @var TwilioWhatsAppConfig */
    public $config;

    public function __construct(TwilioService $twilioService, TwilioWhatsAppConfig $config) {
        $this->twilioService = $twilioService;
        $this->config = $config;
    }

    /**
     * Send an WhatsApp message using the Twilio Service.
     *
     * @param WhatsAppMessage $message
     * @param string $to
     *
     * @return MessageInstance
     * @throws CouldNotSendNotification
     * @throws TwilioException
     */
    public function sendMessage(WhatsAppMessage $message, string $to): MessageInstance {
        $params = [
            'body' => trim($message->content),
        ];

        if ($from = $this->getFrom($message)) {
            $params['from'] = 'whatsapp:' . $from;
        }

        if (empty($from)) {
            throw CouldNotSendNotification::missingFrom();
        }

        $this->fillOptionalParams($params, $message, [
            'statusCallback',
            'statusCallbackMethod'
        ]);

        if ($message instanceof WhatsAppMedia) {
            $this->fillOptionalParams($params, $message, [
                'mediaUrl',
            ]);
        }

        return $this->twilioService->messages->create('whatsapp:' . $to, $params);
    }


    /**
     * Get the from address from message, or config.
     *
     * @param WhatsAppMessage $message
     * @return string|null
     */
    protected function getFrom(WhatsAppMessage $message): ?string {
        return $message->getFrom() ?: $this->config->getFrom();
    }

    /**
     * @param array $params
     * @param WhatsAppMessage $message
     * @param array $optionalParams
     * @return TwilioWhatsApp
     */
    protected function fillOptionalParams(&$params, $message, $optionalParams): self {
        foreach ($optionalParams as $optionalParam) {
            if ($message->$optionalParam) {
                $params[$optionalParam] = $message->$optionalParam;
            }
        }

        return $this;
    }
}
