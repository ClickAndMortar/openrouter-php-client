<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Rerank;

/**
 * @phpstan-type RerankDocumentType array{text: string}
 */
final class RerankDocument
{
    private function __construct(
        public readonly string $text,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            text: is_string($attributes['text'] ?? null) ? $attributes['text'] : '',
        );
    }

    /**
     * @return array{text: string}
     */
    public function toArray(): array
    {
        return ['text' => $this->text];
    }
}
