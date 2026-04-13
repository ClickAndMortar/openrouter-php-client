<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects;

use OpenRouter\Contracts\StringableContract;

/**
 * @internal
 */
final class ResourceUri implements StringableContract
{
    private function __construct(private readonly string $uri)
    {
    }

    public static function create(string $resource): self
    {
        return new self($resource);
    }

    public static function list(string $resource): self
    {
        return new self($resource);
    }

    public static function retrieve(string $resource, string $id, string $suffix = ''): self
    {
        return new self("{$resource}/{$id}{$suffix}");
    }

    public function toString(): string
    {
        return $this->uri;
    }
}
