<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

/**
 * JSON object response format. Mirrors `FormatJsonObjectConfig`.
 */
final class JsonObjectResponseFormat implements ResponseFormat
{
    public function type(): string
    {
        return 'json_object';
    }

    /**
     * @return array{type: string}
     */
    public function toArray(): array
    {
        return ['type' => $this->type()];
    }
}
