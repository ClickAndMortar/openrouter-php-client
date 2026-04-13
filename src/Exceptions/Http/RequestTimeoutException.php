<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 408 — request timed out before the upstream could respond.
 */
final class RequestTimeoutException extends ErrorException
{
}
