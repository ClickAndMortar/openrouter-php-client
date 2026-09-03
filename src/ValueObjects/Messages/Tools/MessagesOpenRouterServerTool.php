<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Tools;

use OpenRouter\ValueObjects\Concerns\OpenRouterToolEnvelope;

/**
 * OpenRouter-hosted server tools available to `/messages`.
 *
 * One class covers the whole `openrouter:*` family: every member shares the
 * `{type, parameters}` wire shape, and the parameter sets change upstream far
 * more often than the envelope. Use the named constructors and pass each
 * tool's documented parameters as an array.
 *
 * `openrouter:web_search` and `openrouter:datetime` keep their dedicated value
 * objects ({@see OpenRouterWebSearchTool}, {@see DatetimeTool}).
 */
final class MessagesOpenRouterServerTool implements MessagesTool
{
    use OpenRouterToolEnvelope;

    /**
     * @var list<string>
     */
    public const TYPES = [
        'openrouter:bash',
        'openrouter:experimental__search_models',
        'openrouter:image_generation',
        'openrouter:shell',
        'openrouter:tool_search',
        'openrouter:web_fetch',
    ];

    /** @param array<string, mixed> $parameters */
    public static function bash(array $parameters = []): self
    {
        return new self('openrouter:bash', $parameters);
    }

    /** @param array<string, mixed> $parameters */
    public static function searchModels(array $parameters = []): self
    {
        return new self('openrouter:experimental__search_models', $parameters);
    }

    /** @param array<string, mixed> $parameters */
    public static function imageGeneration(array $parameters = []): self
    {
        return new self('openrouter:image_generation', $parameters);
    }

    /** @param array<string, mixed> $parameters */
    public static function shell(array $parameters = []): self
    {
        return new self('openrouter:shell', $parameters);
    }

    /** @param array<string, mixed> $parameters */
    public static function toolSearch(array $parameters = []): self
    {
        return new self('openrouter:tool_search', $parameters);
    }

    /** @param array<string, mixed> $parameters */
    public static function webFetch(array $parameters = []): self
    {
        return new self('openrouter:web_fetch', $parameters);
    }
}
