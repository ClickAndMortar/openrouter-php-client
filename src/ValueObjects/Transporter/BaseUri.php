<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Transporter;

use OpenRouter\Contracts\StringableContract;

/**
 * @internal
 */
final class BaseUri implements StringableContract
{
    private function __construct(private readonly string $baseUri)
    {
    }

    public static function from(string $baseUri): self
    {
        return new self($baseUri);
    }

    public function toString(): string
    {
        foreach (['http://', 'https://'] as $protocol) {
            if (str_starts_with($this->baseUri, $protocol)) {
                return rtrim($this->baseUri, '/').'/';
            }
        }

        return 'https://'.rtrim($this->baseUri, '/').'/';
    }
}
