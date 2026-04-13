<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Transporter;

/**
 * @internal
 */
final class QueryParams
{
    /**
     * @param  array<string, string|int>  $params
     */
    private function __construct(private readonly array $params)
    {
    }

    public static function create(): self
    {
        return new self([]);
    }

    public function withParam(string $name, string|int $value): self
    {
        return new self([
            ...$this->params,
            $name => $value,
        ]);
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return $this->params;
    }
}
