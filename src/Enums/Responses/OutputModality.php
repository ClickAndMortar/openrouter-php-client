<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses;

/**
 * Allowed values for the `modalities` request parameter. Superset across
 * request and response surfaces — `text` and `image` are the primary request
 * values; additional cases exist for response-side completeness.
 */
enum OutputModality: string
{
    case Text = 'text';
    case Image = 'image';
    case Embeddings = 'embeddings';
    case Audio = 'audio';
    case Video = 'video';
    case Rerank = 'rerank';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
