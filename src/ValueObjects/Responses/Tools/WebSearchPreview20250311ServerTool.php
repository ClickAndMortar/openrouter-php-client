<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

use OpenRouter\Enums\Responses\Tools\SearchContextSize;
use OpenRouter\Enums\Responses\Tools\WebSearchEngine;
use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Web-search preview server tool (`web_search_preview_2025_03_11` surface).
 * Mirrors the `Preview_20250311_WebSearchServerTool` schema.
 */
final class WebSearchPreview20250311ServerTool implements Tool
{
    /**
     * @param  array<string, mixed>|null  $filters
     * @param  array<string, mixed>|null  $userLocation
     */
    public function __construct(
        public readonly ?WebSearchEngine $engine = null,
        public readonly ?array $filters = null,
        public readonly ?int $maxResults = null,
        public readonly ?SearchContextSize $searchContextSize = null,
        public readonly ?array $userLocation = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $engine = null;
        if (isset($attributes['engine']) && is_string($attributes['engine'])) {
            $engine = WebSearchEngine::tryFrom($attributes['engine']);
            if ($engine === null) {
                throw new InvalidArgumentException(sprintf(
                    'WebSearchPreview20250311ServerTool::$engine must be one of %s, got "%s"',
                    implode('/', WebSearchEngine::values()),
                    $attributes['engine'],
                ));
            }
        }

        $searchContextSize = null;
        if (isset($attributes['search_context_size']) && is_string($attributes['search_context_size'])) {
            $searchContextSize = SearchContextSize::tryFrom($attributes['search_context_size']);
            if ($searchContextSize === null) {
                throw new InvalidArgumentException(sprintf(
                    'WebSearchPreview20250311ServerTool::$searchContextSize must be one of %s, got "%s"',
                    implode('/', SearchContextSize::values()),
                    $attributes['search_context_size'],
                ));
            }
        }

        return new self(
            engine: $engine,
            filters: isset($attributes['filters']) && is_array($attributes['filters']) ? $attributes['filters'] : null,
            maxResults: isset($attributes['max_results']) ? (int) $attributes['max_results'] : null,
            searchContextSize: $searchContextSize,
            userLocation: isset($attributes['user_location']) && is_array($attributes['user_location']) ? $attributes['user_location'] : null,
        );
    }

    public function type(): string
    {
        return 'web_search_preview_2025_03_11';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type()];

        if ($this->engine !== null) {
            $data['engine'] = $this->engine->value;
        }
        if ($this->filters !== null) {
            $data['filters'] = $this->filters;
        }
        if ($this->maxResults !== null) {
            $data['max_results'] = $this->maxResults;
        }
        if ($this->searchContextSize !== null) {
            $data['search_context_size'] = $this->searchContextSize->value;
        }
        if ($this->userLocation !== null) {
            $data['user_location'] = $this->userLocation;
        }

        return $data;
    }
}
