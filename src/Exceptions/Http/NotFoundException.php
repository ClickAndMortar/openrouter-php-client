<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 404 — model or resource not found.
 */
final class NotFoundException extends ErrorException
{
}
