<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * An `openrouter:web_search`-typed item inside the response `output` array.
 * Mirrors the `OutputWebSearchServerToolItem` schema from the OpenRouter
 * OpenAPI spec.
 *
 * Emitted when the OpenRouter-hosted web search server tool runs as part of
 * a response. Distinct from the `web_search_call` output item, which
 * represents a client-side tool invocation.
 *
 * @phpstan-type CreateResponseOutputWebSearchType array{
 *     id?: string,
 *     type: string,
 *     status: string,
 * }
 */
final class CreateResponseOutputWebSearch implements CreateResponseOutputItem
{
    private function __construct(
        public readonly string $id,
        public readonly string $status,
    ) {
    }

    /**
     * @param  CreateResponseOutputWebSearchType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: $attributes['id'] ?? '',
            status: $attributes['status'],
        );
    }

    public function type(): string
    {
        return 'openrouter:web_search';
    }

    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'status' => $this->status,
        ];

        if ($this->id !== '') {
            $data['id'] = $this->id;
        }

        return $data;
    }
}
