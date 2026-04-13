<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Tools;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Chat\Content\ChatCacheControl;

/**
 * A user-defined function the model may call. Mirrors the function variant of
 * `ChatFunctionTool`. Note the wrapping is `{type: function, function: {...}}`,
 * unlike the flat `Responses\Tools\FunctionTool` used by `/responses`.
 */
final class ChatFunctionTool implements ChatTool
{
    private const NAME_MAX_LENGTH = 64;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        public readonly string $name,
        public readonly array $parameters = [],
        public readonly ?string $description = null,
        public readonly ?bool $strict = null,
        public readonly ?ChatCacheControl $cacheControl = null,
    ) {
        if ($this->name === '') {
            throw new InvalidArgumentException('ChatFunctionTool::$name must not be empty');
        }

        if (mb_strlen($this->name) > self::NAME_MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf(
                'ChatFunctionTool::$name must be <= %d characters',
                self::NAME_MAX_LENGTH,
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $function = is_array($attributes['function'] ?? null) ? $attributes['function'] : [];

        return new self(
            name: is_string($function['name'] ?? null) ? $function['name'] : '',
            parameters: is_array($function['parameters'] ?? null) ? $function['parameters'] : [],
            description: isset($function['description']) && is_string($function['description'])
                ? $function['description']
                : null,
            strict: isset($function['strict']) ? (bool) $function['strict'] : null,
            cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                ? ChatCacheControl::from($attributes['cache_control'])
                : null,
        );
    }

    public function type(): string
    {
        return 'function';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $function = [
            'name' => $this->name,
            'parameters' => $this->parameters,
        ];

        if ($this->description !== null) {
            $function['description'] = $this->description;
        }
        if ($this->strict !== null) {
            $function['strict'] = $this->strict;
        }

        $data = [
            'type' => $this->type(),
            'function' => $function,
        ];

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
