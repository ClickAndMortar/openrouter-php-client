<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Guardrails;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `POST /guardrails/{id}/assignments/keys/remove` responses.
 *
 * @phpstan-type BulkUnassignKeysResponseType array{unassigned_count: int}
 *
 * @implements ResponseContract<BulkUnassignKeysResponseType>
 */
final class BulkUnassignKeysResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<BulkUnassignKeysResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly int $unassignedCount,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            unassignedCount: is_int($attributes['unassigned_count'] ?? null) ? $attributes['unassigned_count'] : 0,
            meta: $meta,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['unassigned_count' => $this->unassignedCount];
    }
}
