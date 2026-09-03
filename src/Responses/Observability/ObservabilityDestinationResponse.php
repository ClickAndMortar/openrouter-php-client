<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Observability;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A single observability destination.
 *
 * @phpstan-type ObservabilityDestinationResponseType array<string, mixed>
 *
 * @implements ResponseContract<ObservabilityDestinationResponseType>
 */
final class ObservabilityDestinationResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ObservabilityDestinationResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly ObservabilityDestination $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ObservabilityDestinationResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(ObservabilityDestination::from($raw),
            $meta,
        );
    }

    /**
     * @return ObservabilityDestinationResponseType
     */
    public function toArray(): array
    {
        $data = ['data' => $this->data->toArray()];

        return $data;
    }
}
