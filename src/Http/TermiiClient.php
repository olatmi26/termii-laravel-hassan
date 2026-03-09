<?php

namespace Hassan\Termii\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Hassan\Termii\Exceptions\TermiiApiException;
use Hassan\Termii\Exceptions\TermiiException;

class TermiiClient
{
    protected Client $client;

    public function __construct(
        protected readonly string $baseUrl,
        protected readonly string $apiKey,
        protected readonly int $timeout = 30,
        protected readonly bool $verifySSL = true,
    ) {
        $this->client = new Client([
            'base_uri' => rtrim($baseUrl, '/'),
            'verify'   => $verifySSL,
            'timeout'  => $timeout,
            'headers'  => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ]);
    }

    /**
     * Make a GET request.
     *
     * @throws TermiiException
     */
    public function get(string $endpoint, array $query = []): TermiiResponse
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    /**
     * Make a POST request.
     *
     * @throws TermiiException
     */
    public function post(string $endpoint, array $body = []): TermiiResponse
    {
        return $this->request('POST', $endpoint, ['json' => $body]);
    }

    /**
     * Execute the HTTP request.
     *
     * @throws TermiiException|TermiiApiException
     */
    protected function request(string $method, string $endpoint, array $options = []): TermiiResponse
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
            $body = json_decode((string) $response->getBody(), true) ?? [];

            return new TermiiResponse(
                data: $body,
                statusCode: $response->getStatusCode(),
                successful: $response->getStatusCode() < 400,
            );
        } catch (ClientException $e) {
            $body = json_decode((string) $e->getResponse()->getBody(), true) ?? [];
            $message = $body['message'] ?? $e->getMessage();

            throw new TermiiApiException(
                message: $message,
                statusCode: $e->getResponse()->getStatusCode(),
                context: $body,
                previous: $e,
            );
        } catch (ServerException $e) {
            throw new TermiiApiException(
                message: 'Termii server error: ' . $e->getMessage(),
                statusCode: $e->getResponse()->getStatusCode(),
                previous: $e,
            );
        } catch (ConnectException $e) {
            throw new TermiiException(
                message: 'Unable to connect to Termii API: ' . $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * Get the API key.
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
