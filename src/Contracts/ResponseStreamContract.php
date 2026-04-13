<?php

declare(strict_types=1);

namespace OpenRouter\Contracts;

use IteratorAggregate;

/**
 * @template TResponse
 *
 * @extends IteratorAggregate<int, TResponse>
 */
interface ResponseStreamContract extends IteratorAggregate
{
}
