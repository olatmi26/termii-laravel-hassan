<?php

namespace Hassan\Termii\Http;

use Illuminate\Support\Collection;

class TermiiResponse
{
    public function __construct(
        protected readonly array $data,
        protected readonly int $statusCode,
        protected readonly bool $successful,
    ) {}

    /**
     * Check if the request was successful.
     */
    public function successful(): bool
    {
        return $this->successful;
    }

    /**
     * Check if the request failed.
     */
    public function failed(): bool
    {
        return ! $this->successful;
    }

    /**
     * Get the HTTP status code.
     */
    public function status(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the raw response array.
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Get response as a Collection.
     */
    public function collect(): Collection
    {
        return collect($this->data);
    }

    /**
     * Get a specific key from the response.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->data, $key, $default);
    }

    /**
     * Get the message ID (pin_id or message_id) from the response.
     */
    public function messageId(): ?string
    {
        return $this->get('message_id') ?? $this->get('pinId') ?? $this->get('pin_id');
    }

    /**
     * Get the balance amount from a balance response.
     */
    public function balance(): ?string
    {
        return $this->get('balance');
    }

    /**
     * Determine if the OTP/token was verified.
     */
    public function verified(): bool
    {
        return strtolower((string) $this->get('verified')) === 'true';
    }

    public function __get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function __isset(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function __toString(): string
    {
        return json_encode($this->data);
    }
}
