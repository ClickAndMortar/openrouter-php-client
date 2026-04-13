<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 503 — upstream provider is unavailable or capacity-constrained.
 */
final class ServiceUnavailableException extends ErrorException
{
}
