<?php

declare(strict_types=1);

namespace OpenRouter\Contracts;

use ArrayAccess;

/**
 * @template TArray of array
 *
 * @extends ArrayAccess<key-of<TArray>, value-of<TArray>>
 */
interface ResponseContract extends ArrayAccess
{
    /**
     * @return TArray
     */
    public function toArray(): array;

    public function offsetExists(mixed $offset): bool;

    public function offsetGet(mixed $offset): mixed;

    public function offsetSet(mixed $offset, mixed $value): never;

    public function offsetUnset(mixed $offset): never;
}
