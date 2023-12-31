<?php

declare(strict_types=1);

namespace TwilioWhatsApp\Exceptions;

use TwilioWhatsApp\Messages\WhatsAppMedia;
use TwilioWhatsApp\Messages\WhatsAppMessage;

class CouldNotSendNotification extends \Exception {

    public static function invalidMessageObject($message): self {
        $className = is_object($message) ? get_class($message) : 'Unknown';

        return new static(
            "Notification was not sent. Message object class `{$className}` is invalid. It should
            be either `" . WhatsAppMessage::class . '` or `' . WhatsAppMedia::class . '`'
        );
    }

    public static function missingFrom(): self {
        return new static('Notification was not sent. Missing `from` number.');
    }
}
