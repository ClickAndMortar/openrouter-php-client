<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Citations;

final class CitationFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): MessagesCitation
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';

        return match ($type) {
            'char_location' => CharLocationCitation::from($attributes),
            'page_location' => PageLocationCitation::from($attributes),
            'content_block_location' => ContentBlockLocationCitation::from($attributes),
            'search_result_location' => SearchResultLocationCitation::from($attributes),
            'web_search_result_location' => WebSearchResultLocationCitation::from($attributes),
            default => UnknownCitation::from($attributes),
        };
    }

    /**
     * @param  list<array<string, mixed>>  $attributes
     * @return list<MessagesCitation>
     */
    public static function fromList(array $attributes): array
    {
        return array_values(array_map(
            static fn (array $c): MessagesCitation => self::from($c),
            array_filter($attributes, 'is_array'),
        ));
    }
}
