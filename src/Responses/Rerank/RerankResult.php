<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Rerank;

/**
 * @phpstan-import-type RerankDocumentType from RerankDocument
 *
 * @phpstan-type RerankResultType array{
 *     index: int,
 *     relevance_score: float,
 *     document: RerankDocumentType,
 * }
 */
final class RerankResult
{
    private function __construct(
        public readonly int $index,
        public readonly float $relevanceScore,
        public readonly RerankDocument $document,
        /** @var array<string, mixed> */
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'index',
            'relevance_score',
            'document',
        ]));

        $document = isset($attributes['document']) && is_array($attributes['document'])
            ? RerankDocument::from($attributes['document'])
            : RerankDocument::from([]);

        return new self(
            index: is_int($attributes['index'] ?? null) ? $attributes['index'] : 0,
            relevanceScore: isset($attributes['relevance_score']) && is_numeric($attributes['relevance_score'])
                ? (float) $attributes['relevance_score']
                : 0.0,
            document: $document,
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'index' => $this->index,
            'relevance_score' => $this->relevanceScore,
            'document' => $this->document->toArray(),
        ];

        return [...$data, ...$this->extras];
    }
}
