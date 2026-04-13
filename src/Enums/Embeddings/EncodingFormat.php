<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Embeddings;

/**
 * Allowed values for the `encoding_format` request parameter on
 * `POST /embeddings`. Controls whether returned embedding vectors are
 * JSON float arrays or base64-encoded strings.
 */
enum EncodingFormat: string
{
    case Float = 'float';
    case Base64 = 'base64';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
