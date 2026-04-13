<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Custom grammar response format. Mirrors `ChatFormatGrammarConfig`.
 */
final class GrammarResponseFormat implements ResponseFormat
{
    public function __construct(public readonly string $grammar)
    {
        if ($this->grammar === '') {
            throw new InvalidArgumentException('GrammarResponseFormat::$grammar must not be empty');
        }
    }

    public function type(): string
    {
        return 'grammar';
    }

    /**
     * @return array{type: string, grammar: string}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'grammar' => $this->grammar,
        ];
    }
}
