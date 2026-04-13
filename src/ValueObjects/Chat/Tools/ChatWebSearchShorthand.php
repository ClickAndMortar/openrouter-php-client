<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Tools;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Web search shorthand using OpenAI Responses API syntax. Mirrors
 * `ChatWebSearchShorthand`. Server transparently converts to
 * `openrouter:web_search`.
 */
final class ChatWebSearchShorthand implements ChatTool
{
    public const TYPES = [
        'web_search',
        'web_search_preview',
        'web_search_preview_2025_03_11',
        'web_search_2025_08_26',
    ];

    /**
     * @param  list<string>|null  $allowedDomains
     * @param  list<string>|null  $excludedDomains
     * @param  array<string, mixed>|null  $parameters
     * @param  array<string, mixed>|null  $userLocation
     */
    public function __construct(
        public readonly string $shorthandType = 'web_search',
        public readonly ?array $allowedDomains = null,
        public readonly ?array $excludedDomains = null,
        public readonly ?int $maxResults = null,
        public readonly ?int $maxTotalResults = null,
        public readonly ?string $engine = null,
        public readonly ?string $searchContextSize = null,
        public readonly ?array $parameters = null,
        public readonly ?array $userLocation = null,
    ) {
        if ($this->shorthandType === '') {
            throw new InvalidArgumentException('ChatWebSearchShorthand::$shorthandType must not be empty');
        }
    }

    public function type(): string
    {
        return $this->shorthandType;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->shorthandType];

        foreach ([
            'allowed_domains' => $this->allowedDomains,
            'excluded_domains' => $this->excludedDomains,
            'max_results' => $this->maxResults,
            'max_total_results' => $this->maxTotalResults,
            'engine' => $this->engine,
            'search_context_size' => $this->searchContextSize,
            'parameters' => $this->parameters,
            'user_location' => $this->userLocation,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
