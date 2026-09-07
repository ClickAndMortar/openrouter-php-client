<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Scim;

/**
 * Lifecycle of a SCIM directory sync job.
 *
 * The spec marks this as an open enum, so an unrecognised value decodes to
 * {@see self::Unknown} rather than throwing; the wire value stays reachable
 * on `ScimSyncJob::$rawStatus`.
 */
enum ScimSyncJobStatus: string
{
    case Queued = 'queued';
    case Running = 'running';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case Unknown = 'unknown';

    public static function fromValue(?string $value): self
    {
        if ($value === null) {
            return self::Unknown;
        }

        return self::tryFrom($value) ?? self::Unknown;
    }

    /**
     * Whether the job has stopped, successfully or otherwise. `Unknown` is not
     * treated as terminal — a future status should keep being polled.
     */
    public function isTerminal(): bool
    {
        return $this === self::Succeeded || $this === self::Failed;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
