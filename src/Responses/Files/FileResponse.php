<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Files;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A single stored file. Returned by upload, metadata retrieval, and by
 * promoting a container file into workspace documents.
 *
 * @phpstan-type FileResponseType array<string, mixed>
 *
 * @implements ResponseContract<FileResponseType>
 */
final class FileResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<FileResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly StoredFile $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  FileResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(StoredFile::from($attributes), $meta);
    }

    /**
     * @return FileResponseType
     */
    public function toArray(): array
    {
        return $this->data->toArray();
    }
}
