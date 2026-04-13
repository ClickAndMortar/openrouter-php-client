<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Config;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for the Anthropic `thinking` discriminated union (enabled with
 * a token budget, disabled, or adaptive).
 */
final class MessagesThinkingConfig
{
    public const TYPES = ['enabled', 'disabled', 'adaptive'];

    private function __construct(
        public readonly string $type,
        public readonly ?int $budgetTokens = null,
    ) {
    }

    public static function enabled(int $budgetTokens): self
    {
        if ($budgetTokens < 1) {
            throw new InvalidArgumentException('MessagesThinkingConfig::enabled() $budgetTokens must be >= 1');
        }

        return new self('enabled', $budgetTokens);
    }

    public static function disabled(): self
    {
        return new self('disabled');
    }

    public static function adaptive(): self
    {
        return new self('adaptive');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException(sprintf(
                'MessagesThinkingConfig::$type must be one of %s, got "%s"',
                implode('/', self::TYPES),
                $type,
            ));
        }

        $budget = isset($attributes['budget_tokens']) && is_int($attributes['budget_tokens'])
            ? $attributes['budget_tokens']
            : null;

        if ($type === 'enabled' && $budget === null) {
            throw new InvalidArgumentException('MessagesThinkingConfig with type="enabled" requires budget_tokens');
        }

        return new self($type, $budget);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type];

        if ($this->type === 'enabled' && $this->budgetTokens !== null) {
            $data['budget_tokens'] = $this->budgetTokens;
        }

        return $data;
    }
}
