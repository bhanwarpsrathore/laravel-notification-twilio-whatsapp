# Twilio WhatsApp notifications channel for Laravel


This package makes it easy to send [Twilio WhatsApp notifications](https://www.twilio.com/docs/whatsapp) with 8.x, 9.x & 10.x

## Requirements
* PHP 8.1 or later.

## Installation
Install it using [Composer](https://getcomposer.org/):

```sh
composer require bhanwarpsrathore/laravel-notification-twilio-whatsapp
```

### Configuration

Add your Twilio Account SID, Auth Token, and From Number to your `.env`:

```dotenv
TWILIO_USERNAME=XYZ # optional when using auth token
TWILIO_PASSWORD=ZYX # optional when using auth token
TWILIO_AUTH_TOKEN=ABCD # optional when using username and password
TWILIO_ACCOUNT_SID=1234 # always required
TWILIO_WHATSAPP_FROM=+11111111 # always required
```

### Advanced configuration

You can optionally publish the config file with:
```
php artisan vendor:publish --provider="TwilioWhatsApp\TwilioWhatsAppProvider" --tag="config"
```

#### Suppressing specific errors or all errors

Publish the config using the above command, and edit the `ignored_error_codes` array. You can get the list of
exception codes from [the documentation](https://www.twilio.com/docs/api/errors). 

If you want to suppress all errors, you can set the option to `['*']`. The errors will not be logged.

## License
MIT license. Please see [LICENSE.md](LICENSE.md) for more info.
