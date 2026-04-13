<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Citations;

final class SearchResultLocationCitation implements MessagesCitation
{
    public function __construct(
        public readonly string $citedText,
        public readonly int $startBlockIndex,
        public readonly int $endBlockIndex,
        public readonly ?int $searchResultIndex = null,
        public readonly ?string $title = null,
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
            searchResultIndex: is_int($attributes['search_result_index'] ?? null)
                ? $attributes['search_result_index']
                : null,
            title: isset($attributes['title']) && is_string($attributes['title']) ? $attributes['title'] : null,
        );
    }

    public function type(): string
    {
        return 'search_result_location';
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

        if ($this->searchResultIndex !== null) {
            $data['search_result_index'] = $this->searchResultIndex;
        }
        if ($this->title !== null) {
            $data['title'] = $this->title;
        }

        return $data;
    }
}
