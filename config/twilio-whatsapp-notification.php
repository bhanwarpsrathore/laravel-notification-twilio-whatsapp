<?php

return [
    'username' => env('TWILIO_USERNAME'), // optional when using auth token
    'password' => env('TWILIO_PASSWORD'), // optional when using auth token
    'auth_token' => env('TWILIO_AUTH_TOKEN'), // optional when using username and password
    'account_sid' => env('TWILIO_ACCOUNT_SID'),

    'from' => env('TWILIO_WHATSAPP_FROM'),

    /**
     * If an exception is thrown with one of these error codes, it will be caught & suppressed.
     * Specify '*' in the array, which will cause all exceptions to be suppressed.
     * Suppressed errors will not be logged or reported
     *
     * @see https://www.twilio.com/docs/api/errors
     */
    'ignored_error_codes' => [
        63016, // Failed to send freeform message because you are outside the allowed window. If you are using WhatsApp, please use a Message Template.
        63032, // We cannot send this message to this user because of a WhatsApp limitation. 
    ],
];
