<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Tools;

/**
 * OpenRouter-hosted web-search server tool (`type: openrouter:web_search`).
 * Distinct from {@see WebSearchTool} — this variant uses OpenRouter's
 * infrastructure rather than Anthropic's.
 */
final class OpenRouterWebSearchTool implements MessagesTool
{
    public function __construct(
        public readonly ?int $maxResults = null,
        public readonly ?int $maxTotalResults = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $params = is_array($attributes['parameters'] ?? null) ? $attributes['parameters'] : [];

        return new self(
            maxResults: isset($params['max_results']) && is_int($params['max_results']) ? $params['max_results'] : null,
            maxTotalResults: isset($params['max_total_results']) && is_int($params['max_total_results'])
                ? $params['max_total_results']
                : null,
        );
    }

    public function type(): string
    {
        return 'openrouter:web_search';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type()];

        $params = [];
        if ($this->maxResults !== null) {
            $params['max_results'] = $this->maxResults;
        }
        if ($this->maxTotalResults !== null) {
            $params['max_total_results'] = $this->maxTotalResults;
        }
        if ($params !== []) {
            $data['parameters'] = $params;
        }

        return $data;
    }
}
