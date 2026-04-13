<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class MessagesCreateFixture
{
    /**
     * Mirrors the `MessagesResult` example from openapi-openrouter.yaml (line
     * 15977+), enriched with OpenRouter-specific `usage.cost`, `cost_details`,
     * and `is_byok` fields plus cache-token details for variety.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'id' => 'msg_abc123',
        'type' => 'message',
        'role' => 'assistant',
        'model' => 'anthropic/claude-sonnet-4',
        'content' => [
            [
                'type' => 'text',
                'text' => "I'm doing well, thank you for asking! How can I help you today?",
            ],
        ],
        'stop_reason' => 'end_turn',
        'stop_sequence' => null,
        'usage' => [
            'input_tokens' => 12,
            'output_tokens' => 18,
            'cache_creation_input_tokens' => 0,
            'cache_read_input_tokens' => 0,
            'cost' => 0.0009,
            'cost_details' => [
                'upstream_inference_input_cost' => 0.00036,
                'upstream_inference_output_cost' => 0.00054,
            ],
            'is_byok' => false,
            'service_tier' => 'standard',
            'speed' => 'standard',
        ],
    ];
}
