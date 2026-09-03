<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Tools;

use OpenRouter\ValueObjects\Concerns\OpenRouterToolEnvelope;

/**
 * OpenRouter-hosted server tools available to `/chat/completions`.
 *
 * One class covers the whole `openrouter:*` family: every member shares the
 * `{type, parameters}` wire shape, and the parameter sets change upstream far
 * more often than the envelope. Use the named constructors and pass each
 * tool's documented parameters as an array.
 *
 * `openrouter:web_search` keeps its dedicated shorthand
 * ({@see ChatWebSearchShorthand}).
 */
final class ChatOpenRouterServerTool implements ChatTool
{
    use OpenRouterToolEnvelope;

    /**
     * @var list<string>
     */
    public const TYPES = [
        'openrouter:advisor',
        'openrouter:bash',
        'openrouter:experimental__search_models',
        'openrouter:files',
        'openrouter:fusion',
        'openrouter:image_generation',
        'openrouter:subagent',
        'openrouter:web_fetch',
    ];

    /** @param array<string, mixed> $parameters */
    public static function advisor(array $parameters = []): self
    {
        return new self('openrouter:advisor', $parameters);
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
    public static function subagent(array $parameters = []): self
    {
        return new self('openrouter:subagent', $parameters);
    }

    /** @param array<string, mixed> $parameters */
    public static function webFetch(array $parameters = []): self
    {
        return new self('openrouter:web_fetch', $parameters);
    }
}
