<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;

final class TransporterException extends Exception
{
    public function __construct(ClientExceptionInterface $exception)
    {
        parent::__construct($exception->getMessage(), 0, $exception);
    }
}
