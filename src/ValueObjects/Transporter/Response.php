<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Transporter;

use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @template-covariant TData of array
 *
 * @internal
 */
final readonly class Response
{
    /**
     * @param  TData  $data
     */
    private function __construct(
        private array $data,
        private MetaInformation $meta,
    ) {
    }

    /**
     * @param  TData  $data
     * @param  array<string, array<int, string>>  $headers
     * @return Response<TData>
     */
    public static function from(array $data, array $headers): self
    {
        return new self($data, MetaInformation::from($headers));
    }

    /**
     * @return TData
     */
    public function data(): array
    {
        return $this->data;
    }

    public function meta(): MetaInformation
    {
        return $this->meta;
    }
}
