<?php

namespace Hassan\Termii;

use Hassan\Termii\Http\TermiiClient;
use Hassan\Termii\Http\TermiiResponse;
use Hassan\Termii\Services\InsightService;
use Hassan\Termii\Services\SwitchService;
use Hassan\Termii\Services\TokenService;

/**
 * Termii SDK for Laravel
 *
 * @method TermiiResponse sendMessage(string|array $to, string $sms, ?string $from = null, ?string $channel = null)
 * @method TermiiResponse sendMediaMessage(string $to, string $sms, string $mediaUrl, string $mediaCaption = '', ?string $from = null)
 * @method TermiiResponse sendBulk(array $to, string $sms, ?string $from = null, ?string $channel = null)
 * @method TermiiResponse sendWithNumber(string $to, string $sms)
 * @method TermiiResponse getSenderIds()
 * @method TermiiResponse requestSenderId(string $senderId, string $useCase, string $company)
 * @method TermiiResponse sendOtp(string $to, string $messageText, string $pinPlaceholder = '< 1234 >', int $pinLength = 6, int $pinAttempts = 3, int $pinTimeToLive = 10, string $messageType = 'NUMERIC', ?string $from = null, ?string $channel = null)
 * @method TermiiResponse verifyOtp(string $pinId, string $pin)
 * @method TermiiResponse sendVoiceOtp(string $to, int $pinAttempts = 3, int $pinTimeToLive = 10, int $pinLength = 6)
 * @method TermiiResponse sendVoiceCall(string $to, int $code)
 * @method TermiiResponse sendInAppOtp(string $to, int $pinAttempts = 3, int $pinTimeToLive = 10, int $pinLength = 6, string $pinType = 'NUMERIC')
 * @method TermiiResponse sendEmailOtp(string $emailAddress, string $code, string $emailConfigurationId)
 * @method TermiiResponse balance()
 * @method TermiiResponse history()
 * @method TermiiResponse status(string $phoneNumber, string $countryCode)
 * @method TermiiResponse search(string $phoneNumber)
 */
class Termii
{
    public function __construct(
        protected readonly SwitchService $switch,
        protected readonly TokenService $token,
        protected readonly InsightService $insight,
    ) {}

    /**
     * Access the Switch (messaging) service directly.
     */
    public function switch(): SwitchService
    {
        return $this->switch;
    }

    /**
     * Access the Token (OTP) service directly.
     */
    public function token(): TokenService
    {
        return $this->token;
    }

    /**
     * Access the Insight (analytics) service directly.
     */
    public function insight(): InsightService
    {
        return $this->insight;
    }

    /**
     * Magic method: proxy calls to the appropriate service.
     * Checks Switch, Token, and Insight services in that order.
     */
    public function __call(string $method, array $arguments): mixed
    {
        foreach ([$this->switch, $this->token, $this->insight] as $service) {
            if (method_exists($service, $method)) {
                return $service->{$method}(...$arguments);
            }
        }

        throw new \BadMethodCallException(
            sprintf('Method %s::%s does not exist.', static::class, $method)
        );
    }
}
