<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Datasets;

/**
 * One app in the public token-usage ranking.
 */
final class AppRanking
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly ?int $rank,
        public readonly ?string $appId,
        public readonly ?string $appName,
        public readonly ?int $totalTokens,
        public readonly ?int $totalRequests,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'rank',
            'app_id',
            'app_name',
            'total_tokens',
            'total_requests',
        ]));

        return new self(
            rank: is_int($attributes['rank'] ?? null) ? $attributes['rank'] : null,
            appId: is_string($attributes['app_id'] ?? null) ? $attributes['app_id'] : null,
            appName: is_string($attributes['app_name'] ?? null) ? $attributes['app_name'] : null,
            totalTokens: is_int($attributes['total_tokens'] ?? null) ? $attributes['total_tokens'] : null,
            totalRequests: is_int($attributes['total_requests'] ?? null) ? $attributes['total_requests'] : null,
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ([
            'rank' => $this->rank,
            'app_id' => $this->appId,
            'app_name' => $this->appName,
            'total_tokens' => $this->totalTokens,
            'total_requests' => $this->totalRequests,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
