<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

use OpenRouter\ValueObjects\Responses\Input\Content\ReasoningContentItem;
use OpenRouter\ValueObjects\Responses\Input\Content\ReasoningSummaryItem;

/**
 * A `reasoning`-typed item inside the response `output` array. Mirrors the
 * `OutputReasoningItem` schema from the OpenRouter OpenAPI spec.
 *
 * `content` and `summary` are now hydrated to typed VOs
 * ({@see ReasoningContentItem}, {@see ReasoningSummaryItem}); the raw
 * payload is still recoverable via each VO's `toArray()`.
 *
 * @phpstan-type CreateResponseOutputReasoningType array{
 *     id: string,
 *     type: string,
 *     status?: string|null,
 *     content?: array<int, array<string, mixed>>|null,
 *     summary?: array<int, array<string, mixed>>|null,
 *     format?: string|null,
 *     signature?: string|null,
 *     encrypted_content?: string|null,
 * }
 */
final class CreateResponseOutputReasoning implements CreateResponseOutputItem
{
    /**
     * @param  list<ReasoningContentItem>  $content
     * @param  list<ReasoningSummaryItem>  $summary
     */
    private function __construct(
        public readonly string $id,
        public readonly ?string $status,
        public readonly array $content,
        public readonly array $summary,
        public readonly ?string $format,
        public readonly ?string $signature,
        public readonly ?string $encryptedContent,
    ) {
    }

    /**
     * @param  CreateResponseOutputReasoningType  $attributes
     */
    public static function from(array $attributes): self
    {
        $rawContent = isset($attributes['content']) && is_array($attributes['content']) ? $attributes['content'] : [];
        $rawSummary = isset($attributes['summary']) && is_array($attributes['summary']) ? $attributes['summary'] : [];

        return new self(
            id: $attributes['id'],
            status: $attributes['status'] ?? null,
            content: array_values(array_map(
                static fn (array $item): ReasoningContentItem => ReasoningContentItem::from($item),
                $rawContent,
            )),
            summary: array_values(array_map(
                static fn (array $item): ReasoningSummaryItem => ReasoningSummaryItem::from($item),
                $rawSummary,
            )),
            format: $attributes['format'] ?? null,
            signature: $attributes['signature'] ?? null,
            encryptedContent: $attributes['encrypted_content'] ?? null,
        );
    }

    public function type(): string
    {
        return 'reasoning';
    }

    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type(),
            'summary' => array_map(
                static fn (ReasoningSummaryItem $item): array => $item->toArray(),
                $this->summary,
            ),
        ];

        if ($this->status !== null) {
            $data['status'] = $this->status;
        }
        if ($this->content !== []) {
            $data['content'] = array_map(
                static fn (ReasoningContentItem $item): array => $item->toArray(),
                $this->content,
            );
        }
        if ($this->format !== null) {
            $data['format'] = $this->format;
        }
        if ($this->signature !== null) {
            $data['signature'] = $this->signature;
        }
        if ($this->encryptedContent !== null) {
            $data['encrypted_content'] = $this->encryptedContent;
        }

        return $data;
    }
}
