<?php

declare(strict_types=1);

namespace OpenRouter\Responses;

use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A response whose body is bytes rather than JSON — synthesized speech, a
 * generated video, a downloaded file.
 *
 * The bytes are returned verbatim: no decoding, no transcoding, no assumptions
 * about encoding. `contentType` is the upstream `Content-Type` header, which is
 * how the caller learns the actual format (the audio endpoints vary it with the
 * requested `response_format`).
 */
final class BinaryResponse implements ResponseHasMetaInformationContract
{
    private function __construct(
        public readonly string $contents,
        public readonly string $contentType,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, array<int, string>>  $headers
     */
    public static function from(string $contents, string $contentType, array $headers): self
    {
        return new self($contents, $contentType, MetaInformation::from($headers));
    }

    public function sizeInBytes(): int
    {
        return strlen($this->contents);
    }

    /**
     * Writes the body to `$path` and returns the number of bytes written.
     */
    public function saveTo(string $path): int
    {
        $written = file_put_contents($path, $this->contents);

        if ($written === false) {
            throw new InvalidArgumentException(sprintf('Cannot write to "%s".', $path));
        }

        return $written;
    }

    public function meta(): MetaInformation
    {
        return $this->meta;
    }
}
