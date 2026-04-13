<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input;

use OpenRouter\ValueObjects\Responses\Input\Content\ReasoningContentItem;
use OpenRouter\ValueObjects\Responses\Input\Content\ReasoningSummaryItem;

/**
 * A reasoning input item — used on follow-up requests to return a reasoning
 * block the model produced on a previous turn (required by gpt-5.3-codex and
 * later). Mirrors the `ReasoningItem` / `OutputReasoningItem` (input context)
 * schemas from the OpenRouter OpenAPI spec.
 *
 * Both `summary` and `content` accept either typed VOs ({@see ReasoningSummaryItem},
 * {@see ReasoningContentItem}) or raw arrays for backward compatibility.
 * Raw entries are passed through unchanged on serialization.
 */
final class InputReasoning implements InputItem
{
    /**
     * @param  list<ReasoningSummaryItem|array<string, mixed>>  $summary
     * @param  list<ReasoningContentItem|array<string, mixed>>|null  $content
     */
    public function __construct(
        public readonly string $id,
        public readonly array $summary = [],
        public readonly ?array $content = null,
        public readonly ?string $format = null,
        public readonly ?string $signature = null,
        public readonly ?string $status = null,
        public readonly ?string $encryptedContent = null,
    ) {
    }

    public function type(): string
    {
        return 'reasoning';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'id' => $this->id,
            'summary' => array_map(
                static fn (ReasoningSummaryItem|array $item): array => $item instanceof ReasoningSummaryItem
                    ? $item->toArray()
                    : $item,
                $this->summary,
            ),
        ];

        if ($this->content !== null) {
            $data['content'] = array_map(
                static fn (ReasoningContentItem|array $item): array => $item instanceof ReasoningContentItem
                    ? $item->toArray()
                    : $item,
                $this->content,
            );
        }
        if ($this->format !== null) {
            $data['format'] = $this->format;
        }
        if ($this->signature !== null) {
            $data['signature'] = $this->signature;
        }
        if ($this->status !== null) {
            $data['status'] = $this->status;
        }
        if ($this->encryptedContent !== null) {
            $data['encrypted_content'] = $this->encryptedContent;
        }

        return $data;
    }
}
