<?php

namespace Hassan\Termii\Services;

use Hassan\Termii\Exceptions\TermiiException;
use Hassan\Termii\Http\TermiiClient;
use Hassan\Termii\Http\TermiiResponse;

/**
 * Termii Token API
 *
 * Prevents fraud by generating and verifying one-time-passwords
 * sent to customers' mobile devices.
 */
class TokenService
{
    public const PIN_TYPE_NUMERIC      = 'NUMERIC';
    public const PIN_TYPE_ALPHANUMERIC = 'ALPHANUMERIC';

    public const CHANNEL_GENERIC  = 'generic';
    public const CHANNEL_DND      = 'dnd';
    public const CHANNEL_WHATSAPP = 'whatsapp';

    public function __construct(
        protected readonly TermiiClient $client,
        protected readonly string $defaultSenderId,
        protected readonly string $defaultChannel,
    ) {}

    /**
     * Send an OTP token via SMS, WhatsApp, or voice.
     *
     * @param  string  $to  Recipient phone number in international format
     * @param  string  $messageText  Message body. Must include the placeholder, e.g. "Your OTP is < 1234 >"
     * @param  string  $pinPlaceholder  Placeholder in your message (default: "< 1234 >")
     * @param  int  $pinLength  Length of the OTP (min 4, max 8)
     * @param  int  $pinAttempts  Number of attempts allowed before expiry
     * @param  int  $pinTimeToLive  Token expiry in minutes
     * @param  string  $messageType  PIN_TYPE_NUMERIC or PIN_TYPE_ALPHANUMERIC
     * @param  string|null  $from  Sender ID
     * @param  string|null  $channel  Messaging channel
     *
     * @throws TermiiException
     */
    public function sendOtp(
        string $to,
        string $messageText,
        string $pinPlaceholder = '< 1234 >',
        int $pinLength = 6,
        int $pinAttempts = 3,
        int $pinTimeToLive = 10,
        string $messageType = self::PIN_TYPE_NUMERIC,
        ?string $from = null,
        ?string $channel = null,
    ): TermiiResponse {
        return $this->client->post('/api/sms/otp/send', [
            'api_key'           => $this->client->getApiKey(),
            'message_type'      => $messageType,
            'to'                => $to,
            'from'              => $from ?? $this->defaultSenderId,
            'channel'           => $channel ?? $this->defaultChannel,
            'pin_attempts'      => $pinAttempts,
            'pin_time_to_live'  => $pinTimeToLive,
            'pin_length'        => $pinLength,
            'pin_placeholder'   => $pinPlaceholder,
            'message_text'      => $messageText,
        ]);
    }

    /**
     * Verify / validate an OTP token.
     *
     * @param  string  $pinId  The pin_id returned from sendOtp()
     * @param  string  $pin  The OTP entered by the user
     *
     * @throws TermiiException
     */
    public function verifyOtp(string $pinId, string $pin): TermiiResponse
    {
        return $this->client->post('/api/sms/otp/verify', [
            'api_key' => $this->client->getApiKey(),
            'pin_id'  => $pinId,
            'pin'     => $pin,
        ]);
    }

    /**
     * Send an OTP via a voice call.
     *
     * @param  string  $to  Recipient phone number
     * @param  int  $pinAttempts  Max attempts
     * @param  int  $pinTimeToLive  Expiry in minutes
     * @param  int  $pinLength  Length of OTP
     *
     * @throws TermiiException
     */
    public function sendVoiceOtp(
        string $to,
        int $pinAttempts = 3,
        int $pinTimeToLive = 10,
        int $pinLength = 6,
    ): TermiiResponse {
        return $this->client->post('/api/sms/otp/send/voice', [
            'api_key'          => $this->client->getApiKey(),
            'phone_number'     => $to,
            'pin_attempts'     => $pinAttempts,
            'pin_time_to_live' => $pinTimeToLive,
            'pin_length'       => $pinLength,
        ]);
    }

    /**
     * Make a voice call with a numeric code.
     *
     * @param  string  $to  Recipient phone number
     * @param  int  $code  Numeric code to read out
     *
     * @throws TermiiException
     */
    public function sendVoiceCall(string $to, int $code): TermiiResponse
    {
        return $this->client->post('/api/sms/otp/call', [
            'api_key'      => $this->client->getApiKey(),
            'phone_number' => $to,
            'code'         => $code,
        ]);
    }

    /**
     * Send an in-app OTP (returns the token directly in the API response).
     *
     * @param  string  $to  Recipient phone number
     * @param  int  $pinAttempts  Max attempts
     * @param  int  $pinTimeToLive  Expiry in minutes
     * @param  int  $pinLength  Length of OTP
     * @param  string  $pinType  PIN_TYPE_NUMERIC or PIN_TYPE_ALPHANUMERIC
     *
     * @throws TermiiException
     */
    public function sendInAppOtp(
        string $to,
        int $pinAttempts = 3,
        int $pinTimeToLive = 10,
        int $pinLength = 6,
        string $pinType = self::PIN_TYPE_NUMERIC,
    ): TermiiResponse {
        return $this->client->post('/api/sms/otp/generate', [
            'api_key'          => $this->client->getApiKey(),
            'phone_number'     => $to,
            'pin_attempts'     => $pinAttempts,
            'pin_time_to_live' => $pinTimeToLive,
            'pin_length'       => $pinLength,
            'pin_type'         => $pinType,
        ]);
    }

    /**
     * Send an email OTP token.
     *
     * @param  string  $emailAddress  Recipient's email address
     * @param  string  $code  OTP code
     * @param  string  $emailConfigurationId  Email config ID from Termii dashboard
     *
     * @throws TermiiException
     */
    public function sendEmailOtp(
        string $emailAddress,
        string $code,
        string $emailConfigurationId,
    ): TermiiResponse {
        return $this->client->post('/api/email/otp/send', [
            'api_key'                  => $this->client->getApiKey(),
            'email_address'            => $emailAddress,
            'code'                     => $code,
            'email_configuration_id'   => $emailConfigurationId,
        ]);
    }
}
