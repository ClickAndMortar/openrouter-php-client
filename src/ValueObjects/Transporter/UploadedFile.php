<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Transporter;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * A single file part in a `multipart/form-data` request body.
 *
 * The contents are held in memory: OpenRouter's upload endpoints are metered
 * well below the size where streaming a handle would pay for itself, and an
 * in-memory string keeps {@see Payload} free of resource lifetimes.
 */
final class UploadedFile
{
    public function __construct(
        public readonly string $contents,
        public readonly string $filename,
        public readonly string $contentType = 'application/octet-stream',
    ) {
    }

    /**
     * Reads a file from disk. `$filename` defaults to the basename, which is
     * what the API stores and echoes back.
     */
    public static function fromPath(string $path, ?string $filename = null, ?string $contentType = null): self
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new InvalidArgumentException(sprintf('Cannot read the file "%s".', $path));
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Cannot read the file "%s".', $path));
        }

        return new self(
            $contents,
            $filename ?? basename($path),
            $contentType ?? 'application/octet-stream',
        );
    }

    public static function fromString(string $contents, string $filename, string $contentType = 'application/octet-stream'): self
    {
        return new self($contents, $filename, $contentType);
    }
}
