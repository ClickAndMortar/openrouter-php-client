<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Keys;

/**
 * Shared representation of a single API key record as returned by
 * `/keys`, `/keys/{hash}`, and as `data` on `POST /keys`.
 *
 * @phpstan-type ApiKeyDetailType array{
 *     hash: string,
 *     name: string,
 *     label: string,
 *     disabled: bool,
 *     limit: float,
 *     limit_remaining: float,
 *     limit_reset: ?string,
 *     include_byok_in_limit: bool,
 *     usage: float,
 *     usage_daily: float,
 *     usage_weekly: float,
 *     usage_monthly: float,
 *     byok_usage: float,
 *     byok_usage_daily: float,
 *     byok_usage_weekly: float,
 *     byok_usage_monthly: float,
 *     created_at: string,
 *     updated_at?: ?string,
 *     creator_user_id?: ?string,
 *     expires_at?: ?string,
 * }
 */
final class ApiKeyDetail
{
    private function __construct(
        public readonly string $hash,
        public readonly string $name,
        public readonly string $label,
        public readonly bool $disabled,
        public readonly float $limit,
        public readonly float $limitRemaining,
        public readonly ?string $limitReset,
        public readonly bool $includeByokInLimit,
        public readonly float $usage,
        public readonly float $usageDaily,
        public readonly float $usageWeekly,
        public readonly float $usageMonthly,
        public readonly float $byokUsage,
        public readonly float $byokUsageDaily,
        public readonly float $byokUsageWeekly,
        public readonly float $byokUsageMonthly,
        public readonly string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?string $creatorUserId,
        public readonly ?string $expiresAt,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $float = static fn (mixed $v): float => is_numeric($v) ? (float) $v : 0.0;
        $str = static fn (mixed $v): string => is_string($v) ? $v : '';
        $nullableStr = static fn (mixed $v): ?string => is_string($v) ? $v : null;

        return new self(
            hash: $str($attributes['hash'] ?? null),
            name: $str($attributes['name'] ?? null),
            label: $str($attributes['label'] ?? null),
            disabled: (bool) ($attributes['disabled'] ?? false),
            limit: $float($attributes['limit'] ?? null),
            limitRemaining: $float($attributes['limit_remaining'] ?? null),
            limitReset: $nullableStr($attributes['limit_reset'] ?? null),
            includeByokInLimit: (bool) ($attributes['include_byok_in_limit'] ?? false),
            usage: $float($attributes['usage'] ?? null),
            usageDaily: $float($attributes['usage_daily'] ?? null),
            usageWeekly: $float($attributes['usage_weekly'] ?? null),
            usageMonthly: $float($attributes['usage_monthly'] ?? null),
            byokUsage: $float($attributes['byok_usage'] ?? null),
            byokUsageDaily: $float($attributes['byok_usage_daily'] ?? null),
            byokUsageWeekly: $float($attributes['byok_usage_weekly'] ?? null),
            byokUsageMonthly: $float($attributes['byok_usage_monthly'] ?? null),
            createdAt: $str($attributes['created_at'] ?? null),
            updatedAt: $nullableStr($attributes['updated_at'] ?? null),
            creatorUserId: $nullableStr($attributes['creator_user_id'] ?? null),
            expiresAt: $nullableStr($attributes['expires_at'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'hash' => $this->hash,
            'name' => $this->name,
            'label' => $this->label,
            'disabled' => $this->disabled,
            'limit' => $this->limit,
            'limit_remaining' => $this->limitRemaining,
            'limit_reset' => $this->limitReset,
            'include_byok_in_limit' => $this->includeByokInLimit,
            'usage' => $this->usage,
            'usage_daily' => $this->usageDaily,
            'usage_weekly' => $this->usageWeekly,
            'usage_monthly' => $this->usageMonthly,
            'byok_usage' => $this->byokUsage,
            'byok_usage_daily' => $this->byokUsageDaily,
            'byok_usage_weekly' => $this->byokUsageWeekly,
            'byok_usage_monthly' => $this->byokUsageMonthly,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'creator_user_id' => $this->creatorUserId,
            'expires_at' => $this->expiresAt,
        ];
    }
}
