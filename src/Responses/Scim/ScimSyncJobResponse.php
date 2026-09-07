<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Scim;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A single SCIM directory sync job, as returned when one is started and when
 * it is polled.
 *
 * @phpstan-type ScimSyncJobResponseType array<string, mixed>
 *
 * @implements ResponseContract<ScimSyncJobResponseType>
 */
final class ScimSyncJobResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ScimSyncJobResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly ScimSyncJob $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ScimSyncJobResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(ScimSyncJob::from($raw), $meta);
    }

    /**
     * @return ScimSyncJobResponseType
     */
    public function toArray(): array
    {
        return ['data' => $this->data->toArray()];
    }
}
