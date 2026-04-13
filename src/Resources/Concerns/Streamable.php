<?php

declare(strict_types=1);

namespace OpenRouter\Resources\Concerns;

use OpenRouter\Exceptions\InvalidArgumentException;

trait Streamable
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    private function ensureNotStreamed(array $parameters, string $fallbackFunction = 'sendStreamed'): void
    {
        if (! isset($parameters['stream'])) {
            return;
        }

        if ($parameters['stream'] !== true) {
            return;
        }

        throw new InvalidArgumentException("Stream option is not supported. Please use the $fallbackFunction() method instead.");
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    private function setStreamParameter(array $parameters): array
    {
        $parameters['stream'] = true;

        return $parameters;
    }
}
