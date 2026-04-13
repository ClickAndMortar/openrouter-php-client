<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Citations;

final class ContentBlockLocationCitation implements MessagesCitation
{
    public function __construct(
        public readonly string $citedText,
        public readonly int $startBlockIndex,
        public readonly int $endBlockIndex,
        public readonly ?int $documentIndex = null,
        public readonly ?string $documentTitle = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            citedText: is_string($attributes['cited_text'] ?? null) ? $attributes['cited_text'] : '',
            startBlockIndex: is_int($attributes['start_block_index'] ?? null) ? $attributes['start_block_index'] : 0,
            endBlockIndex: is_int($attributes['end_block_index'] ?? null) ? $attributes['end_block_index'] : 0,
            documentIndex: is_int($attributes['document_index'] ?? null) ? $attributes['document_index'] : null,
            documentTitle: isset($attributes['document_title']) && is_string($attributes['document_title'])
                ? $attributes['document_title']
                : null,
        );
    }

    public function type(): string
    {
        return 'content_block_location';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'cited_text' => $this->citedText,
            'start_block_index' => $this->startBlockIndex,
            'end_block_index' => $this->endBlockIndex,
        ];

        if ($this->documentIndex !== null) {
            $data['document_index'] = $this->documentIndex;
        }
        if ($this->documentTitle !== null) {
            $data['document_title'] = $this->documentTitle;
        }

        return $data;
    }
}
