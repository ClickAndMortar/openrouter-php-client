<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Organization;

enum MemberRole: string
{
    case Admin = 'org:admin';
    case Member = 'org:member';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }
}
