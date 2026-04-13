<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 400 — malformed request, missing fields, or schema violations.
 */
final class BadRequestException extends ErrorException
{
}
