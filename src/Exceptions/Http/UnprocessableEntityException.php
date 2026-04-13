<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions\Http;

use OpenRouter\Exceptions\ErrorException;

/**
 * HTTP 422 — request validated structurally but rejected semantically
 * (e.g. unsupported model parameter combination).
 */
final class UnprocessableEntityException extends ErrorException
{
}
