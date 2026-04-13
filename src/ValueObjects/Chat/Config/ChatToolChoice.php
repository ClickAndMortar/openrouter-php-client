<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for the chat `tool_choice` field. Models the discriminated
 * union of `none`/`auto`/`required` strings or a named-function choice
 * (`ChatNamedToolChoice`).
 *
 * @phpstan-type ChatToolChoiceShape string|array<string, mixed>
 */
final class ChatToolChoice
{
    public const STRING_MODES = ['none', 'auto', 'required'];

    private function __construct(
        public readonly ?string $mode,
        public readonly ?string $functionName,
    ) {
    }

    public static function none(): self
    {
        return new self('none', null);
    }

    public static function auto(): self
    {
        return new self('auto', null);
    }

    public static function required(): self
    {
        return new self('required', null);
    }

    public static function function(string $name): self
    {
        if ($name === '') {
            throw new InvalidArgumentException('ChatToolChoice::function() $name must not be empty');
        }

        return new self(null, $name);
    }

    /**
     * @param  ChatToolChoiceShape  $value
     */
    public static function from(string|array $value): self
    {
        if (is_string($value)) {
            if (! in_array($value, self::STRING_MODES, true)) {
                throw new InvalidArgumentException(sprintf(
                    'ChatToolChoice string must be one of %s, got "%s"',
                    implode('/', self::STRING_MODES),
                    $value,
                ));
            }

            return new self($value, null);
        }

        $type = is_string($value['type'] ?? null) ? $value['type'] : '';
        if ($type !== 'function') {
            throw new InvalidArgumentException(sprintf(
                'ChatToolChoice array must have type="function", got "%s"',
                $type,
            ));
        }

        $function = is_array($value['function'] ?? null) ? $value['function'] : [];
        $name = is_string($function['name'] ?? null) ? $function['name'] : '';

        return self::function($name);
    }

    /**
     * @return ChatToolChoiceShape
     */
    public function toArray(): string|array
    {
        if ($this->mode !== null) {
            return $this->mode;
        }

        return [
            'type' => 'function',
            'function' => ['name' => $this->functionName],
        ];
    }
}
