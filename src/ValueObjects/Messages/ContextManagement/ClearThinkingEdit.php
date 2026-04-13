<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\ContextManagement;

/**
 * `clear_thinking_20251015` edit — clears prior `thinking` blocks from
 * context. `keep` may be:
 *   - an array `{"turns": <int>}` (AnthropicThinkingTurns)
 *   - the string `"all"` (keep every thinking block)
 *   - an array `{"type": "all"}`
 */
final class ClearThinkingEdit implements ContextManagementEdit
{
    /**
     * @param  string|array<string, mixed>|null  $keep
     */
    public function __construct(
        public readonly string|array|null $keep = null,
    ) {
    }

    public static function keepTurns(int $turns): self
    {
        return new self(['turns' => $turns]);
    }

    public static function keepAll(): self
    {
        return new self(['type' => 'all']);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $keep = null;
        if (array_key_exists('keep', $attributes)) {
            $raw = $attributes['keep'];
            if (is_string($raw) || is_array($raw)) {
                $keep = $raw;
            }
        }

        return new self(keep: $keep);
    }

    public function type(): string
    {
        return 'clear_thinking_20251015';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type()];

        if ($this->keep !== null) {
            $data['keep'] = $this->keep;
        }

        return $data;
    }
}
