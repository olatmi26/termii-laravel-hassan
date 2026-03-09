<?php

namespace Hassan\Termii\Facades;

use Illuminate\Support\Facades\Facade;
use Hassan\Termii\Http\TermiiResponse;
use Hassan\Termii\Services\InsightService;
use Hassan\Termii\Services\SwitchService;
use Hassan\Termii\Services\TokenService;

/**
 * @method static SwitchService switch()
 * @method static TokenService token()
 * @method static InsightService insight()
 *
 * -- Switch (Messaging) --
 * @method static TermiiResponse sendMessage(string|array $to, string $sms, ?string $from = null, ?string $channel = null)
 * @method static TermiiResponse sendMediaMessage(string $to, string $sms, string $mediaUrl, string $mediaCaption = '', ?string $from = null)
 * @method static TermiiResponse sendBulk(array $to, string $sms, ?string $from = null, ?string $channel = null)
 * @method static TermiiResponse sendWithNumber(string $to, string $sms)
 * @method static TermiiResponse getSenderIds()
 * @method static TermiiResponse requestSenderId(string $senderId, string $useCase, string $company)
 *
 * -- Token (OTP) --
 * @method static TermiiResponse sendOtp(string $to, string $messageText, string $pinPlaceholder = '< 1234 >', int $pinLength = 6, int $pinAttempts = 3, int $pinTimeToLive = 10, string $messageType = 'NUMERIC', ?string $from = null, ?string $channel = null)
 * @method static TermiiResponse verifyOtp(string $pinId, string $pin)
 * @method static TermiiResponse sendVoiceOtp(string $to, int $pinAttempts = 3, int $pinTimeToLive = 10, int $pinLength = 6)
 * @method static TermiiResponse sendVoiceCall(string $to, int $code)
 * @method static TermiiResponse sendInAppOtp(string $to, int $pinAttempts = 3, int $pinTimeToLive = 10, int $pinLength = 6, string $pinType = 'NUMERIC')
 * @method static TermiiResponse sendEmailOtp(string $emailAddress, string $code, string $emailConfigurationId)
 *
 * -- Insight (Analytics) --
 * @method static TermiiResponse balance()
 * @method static TermiiResponse history()
 * @method static TermiiResponse status(string $phoneNumber, string $countryCode)
 * @method static TermiiResponse search(string $phoneNumber)
 *
 * @see \Hassan\Termii\Termii
 */
class Termii extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'termii';
    }
}
