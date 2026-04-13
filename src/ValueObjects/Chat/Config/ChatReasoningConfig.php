<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Reasoning configuration for the chat completions API. Mirrors the inline
 * `reasoning` object on `ChatRequest` (effort + summary verbosity). Both
 * fields are nullable strings (the spec marks `effort` nullable with `null` as
 * a valid value, matching "no reasoning at all").
 */
final class ChatReasoningConfig
{
    public const EFFORTS = ['xhigh', 'high', 'medium', 'low', 'minimal', 'none'];

    public const SUMMARIES = ['auto', 'concise', 'detailed'];

    public function __construct(
        public readonly ?string $effort = null,
        public readonly ?string $summary = null,
    ) {
        if ($this->effort !== null && ! in_array($this->effort, self::EFFORTS, true)) {
            throw new InvalidArgumentException(sprintf(
                'ChatReasoningConfig::$effort must be one of %s or null, got "%s"',
                implode('/', self::EFFORTS),
                $this->effort,
            ));
        }

        if ($this->summary !== null && ! in_array($this->summary, self::SUMMARIES, true)) {
            throw new InvalidArgumentException(sprintf(
                'ChatReasoningConfig::$summary must be one of %s or null, got "%s"',
                implode('/', self::SUMMARIES),
                $this->summary,
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            effort: isset($attributes['effort']) && is_string($attributes['effort']) ? $attributes['effort'] : null,
            summary: isset($attributes['summary']) && is_string($attributes['summary']) ? $attributes['summary'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->effort !== null) {
            $data['effort'] = $this->effort;
        }
        if ($this->summary !== null) {
            $data['summary'] = $this->summary;
        }

        return $data;
    }
}
