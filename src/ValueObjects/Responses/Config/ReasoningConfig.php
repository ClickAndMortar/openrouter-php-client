<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Config;

use OpenRouter\Enums\Responses\ReasoningEffort;
use OpenRouter\Enums\Responses\ReasoningSummaryVerbosity;
use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for the `reasoning` request field. All fields optional;
 * upstream applies model-appropriate defaults when omitted.
 */
final class ReasoningConfig
{
    public function __construct(
        public readonly ?bool $enabled = null,
        public readonly ?ReasoningEffort $effort = null,
        public readonly ?ReasoningSummaryVerbosity $summary = null,
        public readonly ?int $maxTokens = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $effort = null;
        if (isset($attributes['effort']) && is_string($attributes['effort'])) {
            $effort = ReasoningEffort::tryFrom($attributes['effort']);
            if ($effort === null) {
                throw new InvalidArgumentException(sprintf(
                    'ReasoningConfig::$effort must be one of %s, got "%s"',
                    implode('/', ReasoningEffort::values()),
                    $attributes['effort'],
                ));
            }
        }

        $summary = null;
        if (isset($attributes['summary']) && is_string($attributes['summary'])) {
            $summary = ReasoningSummaryVerbosity::tryFrom($attributes['summary']);
            if ($summary === null) {
                throw new InvalidArgumentException(sprintf(
                    'ReasoningConfig::$summary must be one of %s, got "%s"',
                    implode('/', ReasoningSummaryVerbosity::values()),
                    $attributes['summary'],
                ));
            }
        }

        return new self(
            enabled: isset($attributes['enabled']) ? (bool) $attributes['enabled'] : null,
            effort: $effort,
            summary: $summary,
            maxTokens: isset($attributes['max_tokens']) ? (int) $attributes['max_tokens'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->enabled !== null) {
            $data['enabled'] = $this->enabled;
        }
        if ($this->effort !== null) {
            $data['effort'] = $this->effort->value;
        }
        if ($this->summary !== null) {
            $data['summary'] = $this->summary->value;
        }
        if ($this->maxTokens !== null) {
            $data['max_tokens'] = $this->maxTokens;
        }

        return $data;
    }
}
