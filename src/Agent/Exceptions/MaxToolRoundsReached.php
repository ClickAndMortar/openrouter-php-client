<?php

declare(strict_types=1);

namespace OpenRouter\Agent\Exceptions;

/**
 * Thrown when an agent run hits its `maxToolRounds` ceiling with the model
 * still requesting tool calls. Suppress by setting
 * `ChatAgent::throwOnMaxRounds(false)` / `ResponsesAgent::throwOnMaxRounds(false)`.
 */
final class MaxToolRoundsReached extends \RuntimeException
{
}
