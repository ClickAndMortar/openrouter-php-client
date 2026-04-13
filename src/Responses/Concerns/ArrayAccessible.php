<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Concerns;

use BadMethodCallException;

/**
 * @template TArray of array
 */
trait ArrayAccessible
{
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->toArray());
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new BadMethodCallException('Cannot set response attributes.');
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new BadMethodCallException('Cannot unset response attributes.');
    }
}
