<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Citations;

final class WebSearchResultLocationCitation implements MessagesCitation
{
    public function __construct(
        public readonly string $citedText,
        public readonly ?string $url = null,
        public readonly ?string $title = null,
        public readonly ?string $encryptedIndex = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            citedText: is_string($attributes['cited_text'] ?? null) ? $attributes['cited_text'] : '',
            url: isset($attributes['url']) && is_string($attributes['url']) ? $attributes['url'] : null,
            title: isset($attributes['title']) && is_string($attributes['title']) ? $attributes['title'] : null,
            encryptedIndex: isset($attributes['encrypted_index']) && is_string($attributes['encrypted_index'])
                ? $attributes['encrypted_index']
                : null,
        );
    }

    public function type(): string
    {
        return 'web_search_result_location';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'cited_text' => $this->citedText,
        ];

        foreach ([
            'url' => $this->url,
            'title' => $this->title,
            'encrypted_index' => $this->encryptedIndex,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
