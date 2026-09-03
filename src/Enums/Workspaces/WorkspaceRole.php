<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Workspaces;

/**
 * Role a user holds inside a workspace.
 *
 * Distinct from {@see \OpenRouter\Enums\Organization\MemberRole}, which scopes
 * a user at the organization level and uses `org:`-prefixed values.
 */
enum WorkspaceRole: string
{
    case Admin = 'admin';
    case Member = 'member';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
