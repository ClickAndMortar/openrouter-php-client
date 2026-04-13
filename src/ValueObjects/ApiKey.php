<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects;

use OpenRouter\Contracts\StringableContract;

/**
 * @internal
 */
final class ApiKey implements StringableContract
{
    private function __construct(public readonly string $apiKey)
    {
    }

    public static function from(string $apiKey): self
    {
        return new self($apiKey);
    }

    public function toString(): string
    {
        return $this->apiKey;
    }
}
