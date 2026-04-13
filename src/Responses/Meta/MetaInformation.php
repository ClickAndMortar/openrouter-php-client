<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Meta;

/**
 * OpenRouter response metadata distilled from response headers.
 *
 * OpenRouter forwards a subset of the OpenAI-style rate-limit headers and adds its own
 * `X-Request-Id` / `X-RateLimit-*` set. This value object keeps them accessible without
 * coupling consumers to PSR-7 header arrays.
 */
final class MetaInformation
{
    private function __construct(
        public readonly ?string $requestId,
        public readonly ?int $rateLimitLimit,
        public readonly ?int $rateLimitRemaining,
        public readonly ?string $rateLimitReset,
        /** @var array<string, string> */
        public readonly array $custom,
    ) {
    }

    /**
     * @param  array<string, array<int, string>>  $headers
     */
    public static function from(array $headers): self
    {
        $headers = array_change_key_case($headers, CASE_LOWER);

        $knownHeaders = [
            'x-request-id',
            'x-ratelimit-limit',
            'x-ratelimit-remaining',
            'x-ratelimit-reset',
        ];

        $requestId = $headers['x-request-id'][0] ?? null;
        $limit = isset($headers['x-ratelimit-limit'][0]) ? (int) $headers['x-ratelimit-limit'][0] : null;
        $remaining = isset($headers['x-ratelimit-remaining'][0]) ? (int) $headers['x-ratelimit-remaining'][0] : null;
        $reset = $headers['x-ratelimit-reset'][0] ?? null;

        $custom = [];
        foreach ($headers as $name => $values) {
            if (in_array($name, $knownHeaders, true)) {
                continue;
            }
            $custom[$name] = $values[0] ?? '';
        }

        return new self($requestId, $limit, $remaining, $reset, $custom);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'x-request-id' => $this->requestId,
            'x-ratelimit-limit' => $this->rateLimitLimit,
            'x-ratelimit-remaining' => $this->rateLimitRemaining,
            'x-ratelimit-reset' => $this->rateLimitReset,
            'custom' => $this->custom !== [] ? $this->custom : null,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
