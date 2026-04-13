<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

use OpenRouter\Enums\Responses\Tools\SearchContextSize;
use OpenRouter\Enums\Responses\Tools\WebSearchEngine;
use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Web-search plugin (id: `web`). Note this is the *plugin* surface — for
 * the corresponding *tool* surface see
 * {@see \OpenRouter\ValueObjects\Responses\Tools\WebSearchServerTool}.
 */
final class WebSearchPlugin implements Plugin
{
    /**
     * @param  list<string>|null  $includeDomains
     * @param  list<string>|null  $excludeDomains
     */
    public function __construct(
        public readonly ?bool $enabled = null,
        public readonly ?WebSearchEngine $engine = null,
        public readonly ?int $maxResults = null,
        public readonly ?string $searchPrompt = null,
        public readonly ?array $includeDomains = null,
        public readonly ?array $excludeDomains = null,
        public readonly ?SearchContextSize $searchContextSize = null,
        public readonly ?string $searchQualityLevel = null,
        public readonly ?int $limit = null,
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
                    'WebSearchPlugin::$engine must be one of %s, got "%s"',
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
                    'WebSearchPlugin::$searchContextSize must be one of %s, got "%s"',
                    implode('/', SearchContextSize::values()),
                    $attributes['search_context_size'],
                ));
            }
        }

        return new self(
            enabled: isset($attributes['enabled']) ? (bool) $attributes['enabled'] : null,
            engine: $engine,
            maxResults: isset($attributes['max_results']) ? (int) $attributes['max_results'] : null,
            searchPrompt: isset($attributes['search_prompt']) && is_string($attributes['search_prompt']) ? $attributes['search_prompt'] : null,
            includeDomains: isset($attributes['include_domains']) && is_array($attributes['include_domains']) ? array_values(array_map('strval', $attributes['include_domains'])) : null,
            excludeDomains: isset($attributes['exclude_domains']) && is_array($attributes['exclude_domains']) ? array_values(array_map('strval', $attributes['exclude_domains'])) : null,
            searchContextSize: $searchContextSize,
            searchQualityLevel: isset($attributes['search_quality_level']) && is_string($attributes['search_quality_level']) ? $attributes['search_quality_level'] : null,
            limit: isset($attributes['limit']) ? (int) $attributes['limit'] : null,
        );
    }

    public function id(): string
    {
        return 'web';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['id' => $this->id()];

        $optional = [
            'enabled' => $this->enabled,
            'engine' => $this->engine?->value,
            'max_results' => $this->maxResults,
            'search_prompt' => $this->searchPrompt,
            'include_domains' => $this->includeDomains,
            'exclude_domains' => $this->excludeDomains,
            'search_context_size' => $this->searchContextSize?->value,
            'search_quality_level' => $this->searchQualityLevel,
            'limit' => $this->limit,
        ];

        foreach ($optional as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
