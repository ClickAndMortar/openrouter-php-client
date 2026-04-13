<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Guardrails;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `GET /guardrails/{id}` responses.
 *
 * @phpstan-type GetGuardrailResponseType array{data: array<string, mixed>}
 *
 * @implements ResponseContract<GetGuardrailResponseType>
 */
final class GetGuardrailResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<GetGuardrailResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly Guardrail $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $data = isset($attributes['data']) && is_array($attributes['data'])
            ? Guardrail::from($attributes['data'])
            : Guardrail::from([]);

        return new self(data: $data, meta: $meta);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['data' => $this->data->toArray()];
    }
}
