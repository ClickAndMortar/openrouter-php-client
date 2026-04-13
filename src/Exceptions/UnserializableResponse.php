<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions;

use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface;

final class UnserializableResponse extends Exception
{
    public function __construct(JsonException $exception, public readonly ResponseInterface $response)
    {
        parent::__construct($exception->getMessage(), 0, $exception);
    }
}
