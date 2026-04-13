<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Config;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for the `tool_choice` request field. Models the spec's
 * 6-way union (`OpenAIResponsesToolChoice`):
 *  - `auto` / `required` / `none` string modes
 *  - `{type: function, name}` — pin to a named function tool
 *  - `{type: web_search_preview|web_search_preview_2025_03_11}` — pin to a hosted tool
 *  - `{type: allowed_tools, mode, tools}` — restrict to a subset
 *
 * @phpstan-type ToolChoiceShape string|array<string, mixed>
 */
final class ToolChoice
{
    public const STRING_MODES = ['auto', 'required', 'none'];

    public const ALLOWED_MODES = ['auto', 'required'];

    public const HOSTED_TYPES = ['web_search_preview', 'web_search_preview_2025_03_11'];

    /**
     * @param  list<array<string, mixed>>|null  $allowedTools
     */
    private function __construct(
        public readonly ?string $mode,
        public readonly ?string $allowedMode,
        public readonly ?array $allowedTools,
        public readonly ?string $functionName = null,
        public readonly ?string $hostedType = null,
    ) {
    }

    public static function auto(): self
    {
        return new self(mode: 'auto', allowedMode: null, allowedTools: null);
    }

    public static function required(): self
    {
        return new self(mode: 'required', allowedMode: null, allowedTools: null);
    }

    public static function none(): self
    {
        return new self(mode: 'none', allowedMode: null, allowedTools: null);
    }

    public static function function(string $name): self
    {
        if ($name === '') {
            throw new InvalidArgumentException('ToolChoice::function() $name must not be empty');
        }

        return new self(mode: null, allowedMode: null, allowedTools: null, functionName: $name);
    }

    public static function hosted(string $type): self
    {
        if (! in_array($type, self::HOSTED_TYPES, true)) {
            throw new InvalidArgumentException(sprintf(
                'ToolChoice::hosted() $type must be one of %s, got "%s"',
                implode('/', self::HOSTED_TYPES),
                $type,
            ));
        }

        return new self(mode: null, allowedMode: null, allowedTools: null, hostedType: $type);
    }

    /**
     * @param  list<array<string, mixed>>  $tools
     */
    public static function allowed(array $tools, string $mode = 'auto'): self
    {
        if (! in_array($mode, self::ALLOWED_MODES, true)) {
            throw new InvalidArgumentException(sprintf(
                'ToolChoice::allowed() $mode must be one of %s, got "%s"',
                implode('/', self::ALLOWED_MODES),
                $mode,
            ));
        }

        return new self(mode: null, allowedMode: $mode, allowedTools: $tools);
    }

    /**
     * @param  ToolChoiceShape  $value
     */
    public static function from(string|array $value): self
    {
        if (is_string($value)) {
            if (! in_array($value, self::STRING_MODES, true)) {
                throw new InvalidArgumentException(sprintf(
                    'ToolChoice string must be one of %s, got "%s"',
                    implode('/', self::STRING_MODES),
                    $value,
                ));
            }

            return new self(mode: $value, allowedMode: null, allowedTools: null);
        }

        $type = is_string($value['type'] ?? null) ? $value['type'] : '';

        if ($type === 'function') {
            $name = is_string($value['name'] ?? null) ? $value['name'] : '';

            return self::function($name);
        }

        if (in_array($type, self::HOSTED_TYPES, true)) {
            return self::hosted($type);
        }

        if ($type === 'allowed_tools') {
            $mode = is_string($value['mode'] ?? null) ? $value['mode'] : 'auto';
            $tools = is_array($value['tools'] ?? null) ? $value['tools'] : [];

            return self::allowed($tools, $mode);
        }

        throw new InvalidArgumentException(sprintf(
            'ToolChoice array must have type in [function, %s, allowed_tools], got "%s"',
            implode(', ', self::HOSTED_TYPES),
            $type,
        ));
    }

    /**
     * @return ToolChoiceShape
     */
    public function toArray(): string|array
    {
        if ($this->mode !== null) {
            return $this->mode;
        }

        if ($this->functionName !== null) {
            return [
                'type' => 'function',
                'name' => $this->functionName,
            ];
        }

        if ($this->hostedType !== null) {
            return ['type' => $this->hostedType];
        }

        return [
            'type' => 'allowed_tools',
            'mode' => $this->allowedMode,
            'tools' => $this->allowedTools ?? [],
        ];
    }
}
