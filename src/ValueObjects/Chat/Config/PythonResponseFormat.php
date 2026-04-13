<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

/**
 * Python code response format. Mirrors `ChatFormatPythonConfig`.
 */
final class PythonResponseFormat implements ResponseFormat
{
    public function type(): string
    {
        return 'python';
    }

    /**
     * @return array{type: string}
     */
    public function toArray(): array
    {
        return ['type' => $this->type()];
    }
}
