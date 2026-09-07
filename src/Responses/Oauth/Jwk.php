<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Oauth;

/**
 * One RFC 7517 JSON Web Key from OpenRouter's signing key set.
 *
 * Fields are kept as sent so the key can be handed straight to a JWT library.
 */
final class Jwk
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $kty,
        public readonly string $crv,
        public readonly string $kid,
        public readonly string $x,
        public readonly string $y,
        public readonly string $alg,
        public readonly string $use,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $string = static fn (string $key): string => is_string($attributes[$key] ?? null)
            ? $attributes[$key]
            : '';

        return new self(
            kty: $string('kty'),
            crv: $string('crv'),
            kid: $string('kid'),
            x: $string('x'),
            y: $string('y'),
            alg: $string('alg'),
            use: $string('use'),
            extras: array_diff_key($attributes, array_flip(['kty', 'crv', 'kid', 'x', 'y', 'alg', 'use'])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'kty' => $this->kty,
            'crv' => $this->crv,
            'kid' => $this->kid,
            'x' => $this->x,
            'y' => $this->y,
            'alg' => $this->alg,
            'use' => $this->use,
            ...$this->extras,
        ];
    }
}
