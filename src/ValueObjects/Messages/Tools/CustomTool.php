<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Tools;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Messages\Content\MessagesCacheControl;

/**
 * User-defined tool (`type: custom`). Mirrors the custom variant of
 * `MessagesRequest.tools[]`.
 */
final class CustomTool implements MessagesTool
{
    /**
     * @param  array<string, mixed>  $inputSchema
     */
    public function __construct(
        public readonly string $name,
        public readonly array $inputSchema = [],
        public readonly ?string $description = null,
        public readonly ?MessagesCacheControl $cacheControl = null,
    ) {
        if ($this->name === '') {
            throw new InvalidArgumentException('CustomTool::$name must not be empty');
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : '',
            inputSchema: is_array($attributes['input_schema'] ?? null) ? $attributes['input_schema'] : [],
            description: isset($attributes['description']) && is_string($attributes['description'])
                ? $attributes['description']
                : null,
            cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                ? MessagesCacheControl::from($attributes['cache_control'])
                : null,
        );
    }

    public function type(): string
    {
        return 'custom';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'name' => $this->name,
            'input_schema' => $this->inputSchema,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
