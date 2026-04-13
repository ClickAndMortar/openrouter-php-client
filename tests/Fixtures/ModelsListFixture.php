<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ModelsListFixture
{
    /**
     * Mirrors the `ModelsListResponse` example from openapi-openrouter.yaml
     * for the `/models` endpoint.
     *
     * @var array{data: array<int, array<string, mixed>>}
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'id' => 'openai/gpt-4',
                'canonical_slug' => 'openai/gpt-4',
                'name' => 'GPT-4',
                'description' => 'GPT-4 is a large multimodal model that can solve difficult problems with greater accuracy.',
                'created' => 1692901234,
                'context_length' => 8192,
                'architecture' => [
                    'input_modalities' => ['text'],
                    'output_modalities' => ['text'],
                    'instruct_type' => 'chatml',
                    'modality' => 'text->text',
                    'tokenizer' => 'GPT',
                ],
                'pricing' => [
                    'prompt' => '0.00003',
                    'completion' => '0.00006',
                    'image' => '0',
                    'request' => '0',
                ],
                'top_provider' => [
                    'context_length' => 8192,
                    'is_moderated' => true,
                    'max_completion_tokens' => 4096,
                ],
                'supported_parameters' => ['temperature', 'top_p', 'max_tokens'],
                'per_request_limits' => null,
                'default_parameters' => null,
                'knowledge_cutoff' => null,
                'expiration_date' => null,
                'links' => ['details' => '/api/v1/models/openai/gpt-4/endpoints'],
            ],
        ],
    ];
}
