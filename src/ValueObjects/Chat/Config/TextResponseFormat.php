<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

/**
 * Default text response format. Mirrors `ChatFormatTextConfig`.
 */
final class TextResponseFormat implements ResponseFormat
{
    public function type(): string
    {
        return 'text';
    }

    /**
     * @return array{type: string}
     */
    public function toArray(): array
    {
        return ['type' => $this->type()];
    }
}
