<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Embeddings;

/**
 * A single embedding entry in the `/embeddings` response `data` array. The
 * `embedding` field is either a `list<float>` (when `encoding_format` is
 * `float` or unset) or a base64-encoded string.
 *
 * @phpstan-type EmbeddingDataType array{
 *     object: string,
 *     index: int,
 *     embedding: list<float>|string,
 * }
 */
final class EmbeddingData
{
    /**
     * @param  list<float>|string  $embedding
     */
    private function __construct(
        public readonly string $object,
        public readonly int $index,
        public readonly array|string $embedding,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $rawEmbedding = $attributes['embedding'] ?? [];

        if (is_string($rawEmbedding)) {
            $embedding = $rawEmbedding;
        } elseif (is_array($rawEmbedding)) {
            $embedding = array_values(array_map(
                static fn (mixed $v): float => is_numeric($v) ? (float) $v : 0.0,
                $rawEmbedding,
            ));
        } else {
            $embedding = [];
        }

        return new self(
            object: is_string($attributes['object'] ?? null) ? $attributes['object'] : 'embedding',
            index: is_int($attributes['index'] ?? null) ? $attributes['index'] : 0,
            embedding: $embedding,
        );
    }

    /**
     * @return array{object: string, index: int, embedding: list<float>|string}
     */
    public function toArray(): array
    {
        return [
            'object' => $this->object,
            'index' => $this->index,
            'embedding' => $this->embedding,
        ];
    }
}
