<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Doubles;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * Minimal PSR-3 logger that records every call into a public array.
 *
 * @phpstan-type LogRecord array{level: string, message: string, context: array<string, mixed>}
 */
final class SpyLogger extends AbstractLogger
{
    /**
     * @var list<LogRecord>
     */
    public array $records = [];

    /**
     * @param  array<string, mixed>  $context
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->records[] = [
            'level' => (string) $level,
            'message' => (string) $message,
            'context' => $context,
        ];
    }

    /**
     * @return list<LogRecord>
     */
    public function byLevel(string $level): array
    {
        return array_values(array_filter($this->records, static fn (array $r): bool => $r['level'] === $level));
    }
}
