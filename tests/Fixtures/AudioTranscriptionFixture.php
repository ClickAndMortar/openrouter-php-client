<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class AudioTranscriptionFixture
{
    /**
     * Mirrors the `TranscriptionResponse` shape from openapi-openrouter.yaml
     * for the `POST /audio/transcriptions` endpoint.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'text' => 'Hello from OpenRouter.',
        'task' => 'transcribe',
        'language' => 'en',
        'duration' => 2.5,
        'segments' => [
            [
                'id' => 0,
                'start' => 0.0,
                'end' => 2.5,
                'text' => 'Hello from OpenRouter.',
                'avg_logprob' => -0.21,
                'compression_ratio' => 1.2,
                'no_speech_prob' => 0.01,
                'seek' => 0,
                'temperature' => 0.0,
                'tokens' => [50364, 2425],
            ],
        ],
        'words' => [
            ['word' => 'Hello', 'start' => 0.0, 'end' => 0.4],
        ],
        'usage' => [
            'seconds' => 2.5,
            'input_tokens' => 30,
            'output_tokens' => 6,
            'total_tokens' => 36,
            'cost' => 0.0002,
        ],
    ];
}
