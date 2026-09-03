<?php

declare(strict_types=1);

namespace OpenRouter\Resources\Concerns;

trait Paginates
{
    /**
     * Builds the `limit`/`offset` query shared by the account-level listings,
     * dropping the ones the caller left unset.
     *
     * @return array<string, int>
     */
    private static function page(?int $limit, ?int $offset): array
    {
        return array_filter(
            ['limit' => $limit, 'offset' => $offset],
            static fn (?int $value): bool => $value !== null,
        );
    }
}
