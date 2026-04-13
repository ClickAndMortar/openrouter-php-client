<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

/**
 * Dispatches a raw response-format payload to the correct {@see ResponseFormat}
 * implementation. Unknown types fall back to {@see UnknownResponseFormat}.
 */
final class ResponseFormatFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): ResponseFormat
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';

        return match ($type) {
            'text' => new TextResponseFormat(),
            'json_object' => new JsonObjectResponseFormat(),
            'json_schema' => JsonSchemaResponseFormat::from($attributes),
            'grammar' => new GrammarResponseFormat(
                grammar: is_string($attributes['grammar'] ?? null) ? $attributes['grammar'] : '',
            ),
            'python' => new PythonResponseFormat(),
            default => UnknownResponseFormat::from($attributes),
        };
    }
}
