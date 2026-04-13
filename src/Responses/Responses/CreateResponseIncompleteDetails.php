<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * @phpstan-type CreateResponseIncompleteDetailsType array{reason?: string}
 */
final class CreateResponseIncompleteDetails
{
    private function __construct(public readonly ?string $reason)
    {
    }

    /**
     * @param  CreateResponseIncompleteDetailsType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(reason: $attributes['reason'] ?? null);
    }

    /**
     * @return CreateResponseIncompleteDetailsType
     */
    public function toArray(): array
    {
        return array_filter(['reason' => $this->reason], static fn (mixed $v): bool => $v !== null);
    }
}
