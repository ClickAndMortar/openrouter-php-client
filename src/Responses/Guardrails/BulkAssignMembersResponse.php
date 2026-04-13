<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Guardrails;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `POST /guardrails/{id}/assignments/members` responses.
 *
 * @phpstan-type BulkAssignMembersResponseType array{assigned_count: int}
 *
 * @implements ResponseContract<BulkAssignMembersResponseType>
 */
final class BulkAssignMembersResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<BulkAssignMembersResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly int $assignedCount,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            assignedCount: is_int($attributes['assigned_count'] ?? null) ? $attributes['assigned_count'] : 0,
            meta: $meta,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['assigned_count' => $this->assignedCount];
    }
}
