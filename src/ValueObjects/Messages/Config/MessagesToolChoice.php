<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Config;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for the Anthropic `tool_choice` discriminated union. Supports
 * `auto`, `any`, `none`, and `tool` (named tool). `auto`/`any`/`tool` accept
 * the optional `disable_parallel_tool_use` flag.
 */
final class MessagesToolChoice
{
    public const TYPES = ['auto', 'any', 'none', 'tool'];

    private function __construct(
        public readonly string $type,
        public readonly ?string $name = null,
        public readonly ?bool $disableParallelToolUse = null,
    ) {
    }

    public static function auto(?bool $disableParallelToolUse = null): self
    {
        return new self('auto', null, $disableParallelToolUse);
    }

    public static function any(?bool $disableParallelToolUse = null): self
    {
        return new self('any', null, $disableParallelToolUse);
    }

    public static function none(): self
    {
        return new self('none');
    }

    public static function tool(string $name, ?bool $disableParallelToolUse = null): self
    {
        if ($name === '') {
            throw new InvalidArgumentException('MessagesToolChoice::tool() $name must not be empty');
        }

        return new self('tool', $name, $disableParallelToolUse);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException(sprintf(
                'MessagesToolChoice::$type must be one of %s, got "%s"',
                implode('/', self::TYPES),
                $type,
            ));
        }

        $name = isset($attributes['name']) && is_string($attributes['name']) ? $attributes['name'] : null;
        if ($type === 'tool' && ($name === null || $name === '')) {
            throw new InvalidArgumentException('MessagesToolChoice with type="tool" requires a name');
        }

        $disable = isset($attributes['disable_parallel_tool_use'])
            ? (bool) $attributes['disable_parallel_tool_use']
            : null;

        return new self($type, $name, $disable);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->disableParallelToolUse !== null && $this->type !== 'none') {
            $data['disable_parallel_tool_use'] = $this->disableParallelToolUse;
        }

        return $data;
    }
}
