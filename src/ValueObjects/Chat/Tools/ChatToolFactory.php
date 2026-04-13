<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Tools;

/**
 * Dispatches a raw chat-tool payload to the correct {@see ChatTool}
 * implementation. Unknown types fall back to {@see UnknownChatTool}.
 */
final class ChatToolFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): ChatTool
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';

        return match ($type) {
            'function' => ChatFunctionTool::from($attributes),
            'web_search', 'web_search_preview', 'web_search_preview_2025_03_11', 'web_search_2025_08_26'
                => self::webSearchFrom($attributes),
            default => UnknownChatTool::from($attributes),
        };
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function webSearchFrom(array $attributes): ChatWebSearchShorthand
    {
        return new ChatWebSearchShorthand(
            shorthandType: is_string($attributes['type'] ?? null) ? $attributes['type'] : 'web_search',
            allowedDomains: isset($attributes['allowed_domains']) && is_array($attributes['allowed_domains'])
                ? array_values(array_filter($attributes['allowed_domains'], 'is_string'))
                : null,
            excludedDomains: isset($attributes['excluded_domains']) && is_array($attributes['excluded_domains'])
                ? array_values(array_filter($attributes['excluded_domains'], 'is_string'))
                : null,
            maxResults: isset($attributes['max_results']) && is_int($attributes['max_results'])
                ? $attributes['max_results']
                : null,
            maxTotalResults: isset($attributes['max_total_results']) && is_int($attributes['max_total_results'])
                ? $attributes['max_total_results']
                : null,
            engine: isset($attributes['engine']) && is_string($attributes['engine']) ? $attributes['engine'] : null,
            searchContextSize: isset($attributes['search_context_size']) && is_string($attributes['search_context_size'])
                ? $attributes['search_context_size']
                : null,
            parameters: isset($attributes['parameters']) && is_array($attributes['parameters'])
                ? $attributes['parameters']
                : null,
            userLocation: isset($attributes['user_location']) && is_array($attributes['user_location'])
                ? $attributes['user_location']
                : null,
        );
    }
}
