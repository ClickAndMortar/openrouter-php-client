<?php

declare(strict_types=1);

namespace OpenRouter;

final class OpenRouter
{
    /**
     * Creates a new OpenRouter Client configured with the given API key.
     */
    public static function client(string $apiKey): Client
    {
        return self::factory()->withApiKey($apiKey)->make();
    }

    /**
     * Returns a fluent Factory for building a fully-configured OpenRouter Client.
     */
    public static function factory(): Factory
    {
        return new Factory();
    }
}
