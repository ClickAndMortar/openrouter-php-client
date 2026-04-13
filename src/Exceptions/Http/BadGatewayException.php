<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 502 — bad gateway between OpenRouter and the upstream model provider.
 */
final class BadGatewayException extends ErrorException
{
}
