<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

/**
 * Legacy `openrouter:web_search` server tool. Kept distinct from the new
 * {@see WebSearchServerTool} (2025-08-26) because the OpenAPI spec models
 * them as two separate entries in the tool discriminated union.
 */
final class WebSearchServerToolOpenRouter implements Tool
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
            maxResults: isset($params['max_results']) ? (int) $params['max_results'] : null,
            maxTotalResults: isset($params['max_total_results']) ? (int) $params['max_total_results'] : null,
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
        $params = [];
        if ($this->maxResults !== null) {
            $params['max_results'] = $this->maxResults;
        }
        if ($this->maxTotalResults !== null) {
            $params['max_total_results'] = $this->maxTotalResults;
        }

        $data = ['type' => $this->type()];
        if ($params !== []) {
            $data['parameters'] = $params;
        }

        return $data;
    }
}
