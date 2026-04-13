<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Deltas;

use OpenRouter\ValueObjects\Messages\Citations\CitationFactory;
use OpenRouter\ValueObjects\Messages\Citations\MessagesCitation;

/**
 * `citations_delta` — emitted when a citation is attached to a prior text
 * block. `citation` is a typed {@see MessagesCitation} dispatched from the
 * citation `type` discriminator.
 */
final class CitationsDelta implements MessagesDelta
{
    public function __construct(
        public readonly MessagesCitation $citation,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $citation = is_array($attributes['citation'] ?? null) ? $attributes['citation'] : [];

        return new self(
            citation: CitationFactory::from($citation),
        );
    }

    public function type(): string
    {
        return 'citations_delta';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'citation' => $this->citation->toArray(),
        ];
    }
}
