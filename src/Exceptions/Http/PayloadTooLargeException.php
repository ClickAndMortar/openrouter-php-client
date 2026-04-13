<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 413 — input payload too large (typically context window exceeded).
 */
final class PayloadTooLargeException extends ErrorException
{
}
