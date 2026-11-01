<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Exceptions;

use Exception;
use Throwable;

class BasalamException extends Exception
{
    /**
     * @param array<string, mixed> $responseBody
     * @param array<string, mixed> $errors
     */
    public function __construct(
        string $message = '',
        public readonly int $statusCode = 0,
        public readonly array $responseBody = [],
        public readonly array $errors = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    /**
     * @return array<string, mixed>
     */
    public function getResponseBody(): array
    {
        return $this->responseBody;
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
