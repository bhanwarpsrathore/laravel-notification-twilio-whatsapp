<?php

namespace TwilioWhatsApp\Channels;

use Exception;
use Illuminate\Notifications\Notification;


use TwilioWhatsApp\TwilioWhatsApp;
use TwilioWhatsApp\Messages\WhatsAppMessage;
use TwilioWhatsApp\Exceptions\CouldNotSendNotification;

class TwilioWhatsAppChannel {

    /**
     * @var TwilioWhatsApp
     */
    protected $twilio;

    /**
     * TwilioWhatsAppChannel constructor.
     *
     * @param TwilioWhatsApp $twilio
     * @param Dispatcher $events
     */
    public function __construct(TwilioWhatsApp $twilio) {
        $this->twilio = $twilio;
    }

    public function send($notifiable, Notification $notification) {
        try {
            if (!$to = $notifiable->routeNotificationFor('twilioWhatsApp', $notification)) {
                return;
            }

            $message = $notification->toTwilioWhatsApp($notifiable);

            if (is_string($message)) {
                $message = new WhatsAppMessage($message);
            }

            if (!$message instanceof WhatsAppMessage) {
                throw CouldNotSendNotification::invalidMessageObject($message);
            }

            return $this->twilio->sendMessage($message, $to);
        } catch (Exception $exception) {
            if ($this->twilio->config->isIgnoredErrorCode($exception->getCode())) {
                return;
            }

            throw $exception;
        }
    }
}
