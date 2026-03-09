<?php

namespace Hassan\Termii\Exceptions;

class TermiiApiException extends TermiiException
{
    protected int $statusCode;

    public function __construct(string $message, int $statusCode, array $context = [], ?\Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $context, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
