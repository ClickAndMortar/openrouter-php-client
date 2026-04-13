<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Dispatches a raw plugin payload to the correct {@see Plugin}
 * implementation based on its `id` discriminator. Unknown ids fall back to
 * {@see UnknownPlugin}.
 */
final class PluginFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): Plugin
    {
        $id = is_string($attributes['id'] ?? null) ? $attributes['id'] : '';

        return match ($id) {
            'auto-router' => AutoRouterPlugin::from($attributes),
            'moderation' => ModerationPlugin::from($attributes),
            'web' => WebSearchPlugin::from($attributes),
            'file-parser' => FileParserPlugin::from($attributes),
            'response-healing' => ResponseHealingPlugin::from($attributes),
            'context-compression' => ContextCompressionPlugin::from($attributes),
            default => UnknownPlugin::from($attributes),
        };
    }
}
