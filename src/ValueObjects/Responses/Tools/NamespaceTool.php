<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

/**
 * Groups a set of tools behind a single named namespace, so the model sees one
 * entry instead of every nested tool. Mirrors the `NamespaceTool` schema.
 */
final class NamespaceTool implements Tool
{
    /**
     * @param  list<array<string, mixed>>  $tools
     */
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly array $tools = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        /** @var list<array<string, mixed>> $tools */
        $tools = is_array($attributes['tools'] ?? null)
            ? array_values(array_filter($attributes['tools'], 'is_array'))
            : [];

        return new self(
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : '',
            description: is_string($attributes['description'] ?? null) ? $attributes['description'] : '',
            tools: $tools,
        );
    }

    public function type(): string
    {
        return 'namespace';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'name' => $this->name,
            'description' => $this->description,
            'tools' => $this->tools,
        ];
    }
}
