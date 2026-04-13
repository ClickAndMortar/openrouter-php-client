<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 500 — generic upstream failure.
 */
final class InternalServerErrorException extends ErrorException
{
}
