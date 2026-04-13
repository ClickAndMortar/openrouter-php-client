<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 401 — missing or invalid API key.
 */
final class UnauthorizedException extends ErrorException
{
}
