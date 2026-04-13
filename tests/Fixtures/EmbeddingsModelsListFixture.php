<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class EmbeddingsModelsListFixture
{
    /**
     * Mirrors the 200 example for `GET /embeddings/models` from
     * openapi-openrouter.yaml (shares the `ModelsListResponse` schema).
     *
     * @var array{data: array<int, array<string, mixed>>}
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'id' => 'openai/text-embedding-3-small',
                'canonical_slug' => 'openai/text-embedding-3-small',
                'name' => 'Text Embedding 3 Small',
                'description' => 'OpenAI text embedding model optimized for performance.',
                'created' => 1692901234,
                'context_length' => 8192,
                'architecture' => [
                    'input_modalities' => ['text'],
                    'output_modalities' => ['embeddings'],
                    'instruct_type' => null,
                    'modality' => 'text->text',
                    'tokenizer' => 'GPT',
                ],
                'pricing' => [
                    'prompt' => '0.00000002',
                    'completion' => '0',
                    'image' => '0',
                    'request' => '0',
                ],
                'top_provider' => [
                    'context_length' => 8192,
                    'is_moderated' => false,
                    'max_completion_tokens' => null,
                ],
                'supported_parameters' => [],
                'per_request_limits' => null,
                'default_parameters' => null,
                'knowledge_cutoff' => null,
                'expiration_date' => null,
                'links' => ['details' => '/api/v1/models/openai/text-embedding-3-small/endpoints'],
            ],
        ],
    ];
}
