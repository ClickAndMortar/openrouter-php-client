<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class CreditsRetrieveFixture
{
    /**
     * Mirrors the `CreditsResponse` example from openapi-openrouter.yaml
     * for the `/credits` endpoint.
     *
     * @var array{data: array{total_credits: float, total_usage: float}}
     */
    public const ATTRIBUTES = [
        'data' => [
            'total_credits' => 100.5,
            'total_usage' => 25.75,
        ],
    ];
}
