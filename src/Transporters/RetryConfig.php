<?php

declare(strict_types=1);

namespace OpenRouter\Transporters;

use Closure;
use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Retry policy for {@see HttpTransporter}. Defaults match the OpenRouter
 * `x-retry-strategy` / `x-speakeasy-retries` metadata in the spec:
 * exponential backoff, 3 attempts, 500ms initial delay, 60s max delay,
 * multiplier 1.5, on 5XX responses and connection errors.
 *
 * The `$sleeper` closure is a testability hook — production code relies on
 * the default `usleep` wrapper; tests can inject a fake to avoid real waits.
 */
final class RetryConfig
{
    public readonly Closure $sleeper;

    public function __construct(
        public readonly int $maxAttempts = 3,
        public readonly int $initialDelayMs = 500,
        public readonly int $maxDelayMs = 60_000,
        public readonly float $multiplier = 1.5,
        ?Closure $sleeper = null,
    ) {
        if ($this->maxAttempts < 1) {
            throw new InvalidArgumentException('RetryConfig::$maxAttempts must be >= 1');
        }
        if ($this->initialDelayMs < 0) {
            throw new InvalidArgumentException('RetryConfig::$initialDelayMs must be >= 0');
        }
        if ($this->maxDelayMs < 0) {
            throw new InvalidArgumentException('RetryConfig::$maxDelayMs must be >= 0');
        }
        if ($this->multiplier < 1.0) {
            throw new InvalidArgumentException('RetryConfig::$multiplier must be >= 1.0');
        }

        $this->sleeper = $sleeper ?? static function (int $delayMs): void {
            if ($delayMs > 0) {
                usleep($delayMs * 1000);
            }
        };
    }

    /**
     * Compute the delay (ms) for the Nth attempt (1-indexed). Attempt 1 is
     * the very first request and has no preceding wait.
     */
    public function delayForAttempt(int $attempt): int
    {
        if ($attempt <= 1) {
            return 0;
        }

        $delay = (int) round($this->initialDelayMs * ($this->multiplier ** ($attempt - 2)));

        return min($delay, $this->maxDelayMs);
    }
}
