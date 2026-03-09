<?php

namespace Hassan\Termii\Services;

use Hassan\Termii\Exceptions\TermiiException;
use Hassan\Termii\Http\TermiiClient;
use Hassan\Termii\Http\TermiiResponse;

/**
 * Termii Switch API
 *
 * Allows you to send messages to any country in the world across
 * SMS and WhatsApp channels.
 */
class SwitchService
{
    public function __construct(
        protected readonly TermiiClient $client,
        protected readonly string $defaultSenderId,
        protected readonly string $defaultChannel,
    ) {}

    /**
     * Send a message via SMS or WhatsApp.
     *
     * @param  string|array  $to  Phone number(s) in international format (e.g. 2347012345678)
     * @param  string  $sms  The message content
     * @param  string|null  $from  Sender ID (defaults to config value)
     * @param  string|null  $channel  Messaging channel: generic|dnd|whatsapp (defaults to config value)
     *
     * @throws TermiiException
     */
    public function sendMessage(
        string|array $to,
        string $sms,
        ?string $from = null,
        ?string $channel = null,
    ): TermiiResponse {
        return $this->client->post('/api/sms/send', [
            'api_key' => $this->client->getApiKey(),
            'to'      => is_array($to) ? implode(',', $to) : $to,
            'from'    => $from ?? $this->defaultSenderId,
            'sms'     => $sms,
            'type'    => 'plain',
            'channel' => $channel ?? $this->defaultChannel,
        ]);
    }

    /**
     * Send a message with a media attachment (WhatsApp only).
     *
     * @param  string  $to  Recipient phone number
     * @param  string  $sms  Message body
     * @param  string  $mediaUrl  URL of the media file
     * @param  string  $mediaCaption  Caption for the media
     * @param  string|null  $from  Sender ID
     *
     * @throws TermiiException
     */
    public function sendMediaMessage(
        string $to,
        string $sms,
        string $mediaUrl,
        string $mediaCaption = '',
        ?string $from = null,
    ): TermiiResponse {
        return $this->client->post('/api/sms/send', [
            'api_key'       => $this->client->getApiKey(),
            'to'            => $to,
            'from'          => $from ?? $this->defaultSenderId,
            'sms'           => $sms,
            'type'          => 'plain',
            'channel'       => 'whatsapp',
            'media'         => true,
            'media_url'     => $mediaUrl,
            'media_caption' => $mediaCaption,
        ]);
    }

    /**
     * Send bulk messages to multiple recipients.
     *
     * @param  array  $to  Array of phone numbers
     * @param  string  $sms  Message content
     * @param  string|null  $from  Sender ID
     * @param  string|null  $channel  Channel
     *
     * @throws TermiiException
     */
    public function sendBulk(
        array $to,
        string $sms,
        ?string $from = null,
        ?string $channel = null,
    ): TermiiResponse {
        return $this->client->post('/api/sms/send/bulk', [
            'api_key' => $this->client->getApiKey(),
            'to'      => $to,
            'from'    => $from ?? $this->defaultSenderId,
            'sms'     => $sms,
            'type'    => 'plain',
            'channel' => $channel ?? $this->defaultChannel,
        ]);
    }

    /**
     * Send a message using Termii's auto-generated number.
     *
     * @param  string  $to  Recipient phone number
     * @param  string  $sms  Message content
     *
     * @throws TermiiException
     */
    public function sendWithNumber(string $to, string $sms): TermiiResponse
    {
        return $this->client->post('/api/sms/number/send', [
            'api_key' => $this->client->getApiKey(),
            'to'      => $to,
            'sms'     => $sms,
        ]);
    }

    /**
     * Retrieve all registered sender IDs.
     *
     * @throws TermiiException
     */
    public function getSenderIds(): TermiiResponse
    {
        return $this->client->get('/api/sender-id', [
            'api_key' => $this->client->getApiKey(),
        ]);
    }

    /**
     * Request a new sender ID.
     *
     * @param  string  $senderId  Desired sender ID
     * @param  string  $useCase  How the sender ID will be used
     * @param  string  $company  Company name
     *
     * @throws TermiiException
     */
    public function requestSenderId(
        string $senderId,
        string $useCase,
        string $company,
    ): TermiiResponse {
        return $this->client->post('/api/sender-id/request', [
            'api_key'   => $this->client->getApiKey(),
            'sender_id' => $senderId,
            'usecase'   => $useCase,
            'company'   => $company,
        ]);
    }
}
