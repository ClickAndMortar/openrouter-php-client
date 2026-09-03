<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Datasets;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Median cost per session, by harness and model.
 *
 * `$metadata` is the dataset's own `meta` block (coverage window, generation
 * time); `meta()` remains the HTTP response metadata, as on every response.
 *
 * @phpstan-type SessionCostResponseType array<string, mixed>
 *
 * @implements ResponseContract<SessionCostResponseType>
 */
final class SessionCostResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<SessionCostResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, SessionCost>  $data
     * @param  array<string, mixed>  $metadata
     */
    private function __construct(
        public readonly array $data,
        public readonly array $metadata,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  SessionCostResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(
            array_values(array_map(
                static fn (array $item): SessionCost => SessionCost::from($item),
                array_filter($raw, 'is_array'),
            )),
            is_array($attributes['meta'] ?? null) ? $attributes['meta'] : [],
            $meta,
        );
    }

    /**
     * @return SessionCostResponseType
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(static fn (SessionCost $i): array => $i->toArray(), $this->data),
            'meta' => $this->metadata,
        ];
    }
}
