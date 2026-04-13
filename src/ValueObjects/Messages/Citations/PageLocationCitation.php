<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Citations;

final class PageLocationCitation implements MessagesCitation
{
    public function __construct(
        public readonly string $citedText,
        public readonly int $startPageNumber,
        public readonly int $endPageNumber,
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
            startPageNumber: is_int($attributes['start_page_number'] ?? null) ? $attributes['start_page_number'] : 0,
            endPageNumber: is_int($attributes['end_page_number'] ?? null) ? $attributes['end_page_number'] : 0,
            documentIndex: is_int($attributes['document_index'] ?? null) ? $attributes['document_index'] : null,
            documentTitle: isset($attributes['document_title']) && is_string($attributes['document_title'])
                ? $attributes['document_title']
                : null,
        );
    }

    public function type(): string
    {
        return 'page_location';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'cited_text' => $this->citedText,
            'start_page_number' => $this->startPageNumber,
            'end_page_number' => $this->endPageNumber,
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
