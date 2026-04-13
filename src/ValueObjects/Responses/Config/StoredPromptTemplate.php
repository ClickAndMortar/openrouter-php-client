<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Config;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for the `prompt` request field — references a stored prompt
 * template with optional variable substitutions.
 */
final class StoredPromptTemplate
{
    /**
     * @param  array<string, string|array<string, mixed>>|null  $variables
     */
    public function __construct(
        public readonly string $id,
        public readonly ?array $variables = null,
    ) {
        if ($this->id === '') {
            throw new InvalidArgumentException('StoredPromptTemplate::$id must not be empty');
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            variables: isset($attributes['variables']) && is_array($attributes['variables']) ? $attributes['variables'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['id' => $this->id];

        if ($this->variables !== null) {
            $data['variables'] = $this->variables;
        }

        return $data;
    }
}
