<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ModelsCountFixture
{
    /**
     * Mirrors the `ModelsCountResponse` example from openapi-openrouter.yaml
     * for the `/models/count` endpoint.
     *
     * @var array{data: array{count: int}}
     */
    public const ATTRIBUTES = [
        'data' => [
            'count' => 150,
        ],
    ];
}
