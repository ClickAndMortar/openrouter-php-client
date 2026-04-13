<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 429 — rate-limited. Caller should back off and retry.
 */
final class TooManyRequestsException extends ErrorException
{
}
