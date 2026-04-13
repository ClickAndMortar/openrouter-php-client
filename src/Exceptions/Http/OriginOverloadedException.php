<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 529 — upstream origin overloaded.
 */
final class OriginOverloadedException extends ErrorException
{
}
