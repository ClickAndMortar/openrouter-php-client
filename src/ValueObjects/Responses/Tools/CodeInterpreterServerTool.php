<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

/**
 * Code-interpreter server tool. `container` is either the literal string
 * "auto" or an object with `type: 'auto'`, `file_ids[]`, and `memory_limit`.
 */
final class CodeInterpreterServerTool implements Tool
{
    /**
     * @param  array<string, mixed>|string  $container
     */
    public function __construct(
        public readonly array|string $container = 'auto',
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $container = $attributes['container'] ?? 'auto';

        return new self(
            container: is_array($container) || is_string($container) ? $container : 'auto',
        );
    }

    public function type(): string
    {
        return 'code_interpreter';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'container' => $this->container,
        ];
    }
}
