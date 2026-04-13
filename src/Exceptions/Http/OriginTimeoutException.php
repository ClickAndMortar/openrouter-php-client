<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 524 — Cloudflare-style origin timeout while waiting on the upstream
 * model provider.
 */
final class OriginTimeoutException extends ErrorException
{
}
