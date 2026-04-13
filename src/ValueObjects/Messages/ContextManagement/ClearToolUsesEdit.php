<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\ContextManagement;

/**
 * `clear_tool_uses_20250919` edit — clears prior tool uses from context when
 * a trigger fires. `trigger`, `keep`, `clearAtLeast`, and `clearToolInputs`
 * are passed through as raw values because they themselves reference nested
 * discriminated unions in the schema.
 */
final class ClearToolUsesEdit implements ContextManagementEdit
{
    /**
     * @param  array<string, mixed>|null  $keep
     * @param  array<string, mixed>|null  $clearAtLeast
     * @param  array<string, mixed>|null  $trigger
     * @param  list<string>|null  $excludeTools
     * @param  bool|list<string>|null  $clearToolInputs
     */
    public function __construct(
        public readonly ?array $keep = null,
        public readonly ?array $clearAtLeast = null,
        public readonly bool|array|null $clearToolInputs = null,
        public readonly ?array $excludeTools = null,
        public readonly ?array $trigger = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $clearToolInputs = null;
        if (array_key_exists('clear_tool_inputs', $attributes)) {
            $raw = $attributes['clear_tool_inputs'];
            if (is_bool($raw)) {
                $clearToolInputs = $raw;
            } elseif (is_array($raw)) {
                $clearToolInputs = array_values(array_filter($raw, 'is_string'));
            }
        }

        return new self(
            keep: isset($attributes['keep']) && is_array($attributes['keep']) ? $attributes['keep'] : null,
            clearAtLeast: isset($attributes['clear_at_least']) && is_array($attributes['clear_at_least'])
                ? $attributes['clear_at_least']
                : null,
            clearToolInputs: $clearToolInputs,
            excludeTools: isset($attributes['exclude_tools']) && is_array($attributes['exclude_tools'])
                ? array_values(array_filter($attributes['exclude_tools'], 'is_string'))
                : null,
            trigger: isset($attributes['trigger']) && is_array($attributes['trigger'])
                ? $attributes['trigger']
                : null,
        );
    }

    public function type(): string
    {
        return 'clear_tool_uses_20250919';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type()];

        foreach ([
            'keep' => $this->keep,
            'clear_at_least' => $this->clearAtLeast,
            'clear_tool_inputs' => $this->clearToolInputs,
            'exclude_tools' => $this->excludeTools,
            'trigger' => $this->trigger,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
