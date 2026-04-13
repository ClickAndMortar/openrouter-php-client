<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses\Tools;

/**
 * Allowed values for the `environment` field on the computer-use tool.
 */
enum ComputerEnvironment: string
{
    case Windows = 'windows';
    case Mac = 'mac';
    case Linux = 'linux';
    case Ubuntu = 'ubuntu';
    case Browser = 'browser';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
