<?php

namespace TwilioWhatsApp\Channels;

use Exception;
use Illuminate\Notifications\Notification;


use TwilioWhatsApp\TwilioWhatsApp;
use TwilioWhatsApp\Messages\WhatsAppMessage;
use TwilioWhatsApp\Exceptions\CouldNotSendNotification;

class TwilioWhatsAppChannel {

    /**
     * @var Twilio
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
            $to = $this->getTo($notifiable, $notification);
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

    /**
     * Get the address to send a notification to.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @return mixed
     * @throws CouldNotSendNotification
     */
    protected function getTo($notifiable, Notification $notification) {
        if ($notifiable->routeNotificationFor('twilioWhatsApp', $notification)) {
            return $notifiable->routeNotificationFor('twilioWhatsApp', $notification);
        }
        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }

        throw CouldNotSendNotification::invalidReceiver();
    }
}
