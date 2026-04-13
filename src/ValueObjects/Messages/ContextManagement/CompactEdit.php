<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\ContextManagement;

/**
 * `compact_20260112` edit — compacts context history when a token trigger
 * fires. `trigger` references the `AnthropicInputTokensTrigger` shape and is
 * passed through as a raw array.
 */
final class CompactEdit implements ContextManagementEdit
{
    /**
     * @param  array<string, mixed>|null  $trigger
     */
    public function __construct(
        public readonly ?string $instructions = null,
        public readonly ?bool $pauseAfterCompaction = null,
        public readonly ?array $trigger = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            instructions: isset($attributes['instructions']) && is_string($attributes['instructions'])
                ? $attributes['instructions']
                : null,
            pauseAfterCompaction: isset($attributes['pause_after_compaction'])
                ? (bool) $attributes['pause_after_compaction']
                : null,
            trigger: isset($attributes['trigger']) && is_array($attributes['trigger'])
                ? $attributes['trigger']
                : null,
        );
    }

    public function type(): string
    {
        return 'compact_20260112';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type()];

        foreach ([
            'instructions' => $this->instructions,
            'pause_after_compaction' => $this->pauseAfterCompaction,
            'trigger' => $this->trigger,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
