<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages;

/**
 * Token usage statistics for a `/messages` response. Mirrors Anthropic's
 * `AnthropicUsage` plus OpenRouter's extended `cost`, `cost_details`,
 * `is_byok`, `iterations`, `service_tier`, and `speed` fields.
 *
 * Unlike {@see \OpenRouter\Responses\Chat\ChatUsage}, Anthropic uses
 * `input_tokens`/`output_tokens` naming rather than OpenAI's
 * `prompt_tokens`/`completion_tokens`.
 */
final class MessagesUsage
{
    /**
     * @param  array<string, mixed>|null  $serverToolUse
     * @param  array<string, mixed>|null  $costDetails
     * @param  list<array<string, mixed>>|null  $iterations
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly int $inputTokens,
        public readonly int $outputTokens,
        public readonly ?int $cacheCreationInputTokens,
        public readonly ?int $cacheReadInputTokens,
        public readonly ?array $serverToolUse,
        public readonly ?float $cost,
        public readonly ?array $costDetails,
        public readonly ?bool $isByok,
        public readonly ?array $iterations,
        public readonly ?string $serviceTier,
        public readonly ?string $speed,
        public readonly array $extras,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $known = [
            'input_tokens',
            'output_tokens',
            'cache_creation_input_tokens',
            'cache_read_input_tokens',
            'server_tool_use',
            'cost',
            'cost_details',
            'is_byok',
            'iterations',
            'service_tier',
            'speed',
        ];
        $extras = array_diff_key($attributes, array_flip($known));

        $iterations = null;
        if (isset($attributes['iterations']) && is_array($attributes['iterations'])) {
            $iterations = array_values(array_filter($attributes['iterations'], 'is_array'));
        }

        return new self(
            inputTokens: is_int($attributes['input_tokens'] ?? null) ? $attributes['input_tokens'] : 0,
            outputTokens: is_int($attributes['output_tokens'] ?? null) ? $attributes['output_tokens'] : 0,
            cacheCreationInputTokens: is_int($attributes['cache_creation_input_tokens'] ?? null)
                ? $attributes['cache_creation_input_tokens']
                : null,
            cacheReadInputTokens: is_int($attributes['cache_read_input_tokens'] ?? null)
                ? $attributes['cache_read_input_tokens']
                : null,
            serverToolUse: isset($attributes['server_tool_use']) && is_array($attributes['server_tool_use'])
                ? $attributes['server_tool_use']
                : null,
            cost: isset($attributes['cost']) ? (float) $attributes['cost'] : null,
            costDetails: isset($attributes['cost_details']) && is_array($attributes['cost_details'])
                ? $attributes['cost_details']
                : null,
            isByok: isset($attributes['is_byok']) ? (bool) $attributes['is_byok'] : null,
            iterations: $iterations,
            serviceTier: is_string($attributes['service_tier'] ?? null) ? $attributes['service_tier'] : null,
            speed: is_string($attributes['speed'] ?? null) ? $attributes['speed'] : null,
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'input_tokens' => $this->inputTokens,
            'output_tokens' => $this->outputTokens,
        ];

        foreach ([
            'cache_creation_input_tokens' => $this->cacheCreationInputTokens,
            'cache_read_input_tokens' => $this->cacheReadInputTokens,
            'server_tool_use' => $this->serverToolUse,
            'cost' => $this->cost,
            'cost_details' => $this->costDetails,
            'is_byok' => $this->isByok,
            'iterations' => $this->iterations,
            'service_tier' => $this->serviceTier,
            'speed' => $this->speed,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
