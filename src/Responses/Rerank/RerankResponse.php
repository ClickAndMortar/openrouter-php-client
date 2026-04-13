<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Rerank;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around OpenRouter's rerank response schema for `POST /rerank`.
 *
 * @phpstan-import-type RerankResultType from RerankResult
 * @phpstan-import-type RerankUsageType from RerankUsage
 *
 * @phpstan-type RerankResponseType array{
 *     id?: string,
 *     model: string,
 *     provider?: string,
 *     results: list<RerankResultType>,
 *     usage?: RerankUsageType,
 * }
 *
 * @implements ResponseContract<RerankResponseType>
 */
final class RerankResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<RerankResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  list<RerankResult>  $results
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $model,
        public readonly ?string $provider,
        public readonly array $results,
        public readonly ?RerankUsage $usage,
        public readonly array $extras,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $rawResults = isset($attributes['results']) && is_array($attributes['results'])
            ? $attributes['results']
            : [];

        $results = array_values(array_map(
            static fn (array $item): RerankResult => RerankResult::from($item),
            array_filter($rawResults, 'is_array'),
        ));

        $known = ['id', 'model', 'provider', 'results', 'usage'];
        $extras = array_diff_key($attributes, array_flip($known));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            model: is_string($attributes['model'] ?? null) ? $attributes['model'] : '',
            provider: isset($attributes['provider']) && is_string($attributes['provider']) ? $attributes['provider'] : null,
            results: $results,
            usage: isset($attributes['usage']) && is_array($attributes['usage'])
                ? RerankUsage::from($attributes['usage'])
                : null,
            extras: $extras,
            meta: $meta,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $out = [
            'id' => $this->id,
            'model' => $this->model,
            'results' => array_map(static fn (RerankResult $r): array => $r->toArray(), $this->results),
        ];

        if ($this->provider !== null) {
            $out['provider'] = $this->provider;
        }

        if ($this->usage !== null) {
            $out['usage'] = $this->usage->toArray();
        }

        return [...$out, ...$this->extras];
    }
}
