<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

/**
 * @phpstan-type ListResponseModelTopProviderType array{
 *     context_length?: int|null,
 *     is_moderated: bool,
 *     max_completion_tokens?: int|null,
 * }
 */
final class ListResponseModelTopProvider
{
    private function __construct(
        public readonly bool $isModerated,
        public readonly ?int $contextLength,
        public readonly ?int $maxCompletionTokens,
    ) {
    }

    /**
     * @param  ListResponseModelTopProviderType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            isModerated: $attributes['is_moderated'],
            contextLength: $attributes['context_length'] ?? null,
            maxCompletionTokens: $attributes['max_completion_tokens'] ?? null,
        );
    }

    /**
     * @return ListResponseModelTopProviderType
     */
    public function toArray(): array
    {
        $data = ['is_moderated' => $this->isModerated];

        if ($this->contextLength !== null) {
            $data['context_length'] = $this->contextLength;
        }

        if ($this->maxCompletionTokens !== null) {
            $data['max_completion_tokens'] = $this->maxCompletionTokens;
        }

        /** @var ListResponseModelTopProviderType $data */
        return $data;
    }
}
