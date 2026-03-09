<?php

namespace Hassan\Termii\Services;

use Hassan\Termii\Exceptions\TermiiException;
use Hassan\Termii\Http\TermiiClient;
use Hassan\Termii\Http\TermiiResponse;

/**
 * Termii Insight API
 *
 * Provides real-time delivery status, customer analytics,
 * and phone number intelligence.
 */
class InsightService
{
    public function __construct(
        protected readonly TermiiClient $client,
    ) {}

    /**
     * Get your Termii account balance.
     *
     * @throws TermiiException
     */
    public function balance(): TermiiResponse
    {
        return $this->client->get('/api/get-balance', [
            'api_key' => $this->client->getApiKey(),
        ]);
    }

    /**
     * Get reports for messages sent across SMS, voice & WhatsApp channels.
     *
     * @throws TermiiException
     */
    public function history(): TermiiResponse
    {
        return $this->client->get('/api/sms/inbox', [
            'api_key' => $this->client->getApiKey(),
        ]);
    }

    /**
     * Detect if a number is fake or has ported to a new network.
     *
     * @param  string  $phoneNumber  Phone number to check (without country code)
     * @param  string  $countryCode  ISO country code, e.g. "NG" for Nigeria
     *
     * @throws TermiiException
     */
    public function status(string $phoneNumber, string $countryCode): TermiiResponse
    {
        return $this->client->get('/api/insight/number/query', [
            'api_key'      => $this->client->getApiKey(),
            'phone_number' => $phoneNumber,
            'country_code' => $countryCode,
        ]);
    }

    /**
     * Verify a phone number and automatically detect its status.
     * Also reveals if the number has activated DND (Do Not Disturb).
     *
     * @param  string  $phoneNumber  Phone number in international format
     *
     * @throws TermiiException
     */
    public function search(string $phoneNumber): TermiiResponse
    {
        return $this->client->get('/api/check/dnd', [
            'api_key'      => $this->client->getApiKey(),
            'phone_number' => $phoneNumber,
        ]);
    }
}
