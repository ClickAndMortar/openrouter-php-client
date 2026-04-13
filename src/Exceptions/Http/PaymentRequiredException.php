<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 402 — insufficient credits or billing required.
 */
final class PaymentRequiredException extends ErrorException
{
}
