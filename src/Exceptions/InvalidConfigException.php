<?php

declare(strict_types=1);

namespace TwilioWhatsApp\Exceptions;

class InvalidConfigException extends \Exception {

    public static function missingConfig(): self {
        return new self('Missing config. You must set either the username & password or SID and auth token');
    }
}
