<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Guardrails;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `GET /guardrails` responses.
 *
 * @phpstan-type ListGuardrailsResponseType array{data: list<array<string, mixed>>, total_count: int}
 *
 * @implements ResponseContract<ListGuardrailsResponseType>
 */
final class ListGuardrailsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListGuardrailsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  list<Guardrail>  $data
     */
    private function __construct(
        public readonly array $data,
        public readonly int $totalCount,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = isset($attributes['data']) && is_array($attributes['data']) ? $attributes['data'] : [];

        $data = array_values(array_map(
            static fn (array $item): Guardrail => Guardrail::from($item),
            array_filter($raw, 'is_array'),
        ));

        $totalCount = is_int($attributes['total_count'] ?? null) ? $attributes['total_count'] : 0;

        return new self(data: $data, totalCount: $totalCount, meta: $meta);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(static fn (Guardrail $g): array => $g->toArray(), $this->data),
            'total_count' => $this->totalCount,
        ];
    }
}
