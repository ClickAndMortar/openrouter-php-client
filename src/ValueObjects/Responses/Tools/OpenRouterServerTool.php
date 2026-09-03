<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

use OpenRouter\ValueObjects\Concerns\OpenRouterToolEnvelope;

/**
 * OpenRouter-hosted server tools that share the `{type, parameters}` envelope.
 *
 * The whole `openrouter:*` family is modelled by this one class rather than by
 * a class per tool: every member has the same wire shape, and the parameter
 * sets change upstream far more often than the envelope does. Use the named
 * constructors for discoverability, and pass the tool's documented parameters
 * as an array.
 *
 * `openrouter:web_search` and `openrouter:datetime` predate this family and
 * keep their dedicated value objects ({@see WebSearchServerToolOpenRouter},
 * {@see DatetimeServerTool}).
 */
final class OpenRouterServerTool implements Tool
{
    use OpenRouterToolEnvelope;

    /**
     * Every `openrouter:*` tool accepted in a `/responses` request that does
     * not already have a dedicated value object.
     *
     * @var list<string>
     */
    public const TYPES = [
        'openrouter:advisor',
        'openrouter:apply_patch',
        'openrouter:bash',
        'openrouter:experimental__search_models',
        'openrouter:files',
        'openrouter:fusion',
        'openrouter:image_generation',
        'openrouter:shell',
        'openrouter:subagent',
        'openrouter:tool_search',
        'openrouter:web_fetch',
    ];

    /** @param array<string, mixed> $parameters */
    public static function advisor(array $parameters = []): self
    {
        return new self('openrouter:advisor', $parameters);
    }

    /** @param array<string, mixed> $parameters */
    public static function applyPatch(array $parameters = []): self
    {
        return new self('openrouter:apply_patch', $parameters);
    }

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
    public static function files(array $parameters = []): self
    {
        return new self('openrouter:files', $parameters);
    }

    /** @param array<string, mixed> $parameters */
    public static function fusion(array $parameters = []): self
    {
        return new self('openrouter:fusion', $parameters);
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
    public static function subagent(array $parameters = []): self
    {
        return new self('openrouter:subagent', $parameters);
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
