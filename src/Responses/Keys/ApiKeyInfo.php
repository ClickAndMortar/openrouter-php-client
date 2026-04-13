<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Keys;

/**
 * Representation of the current API key as returned by `GET /key`.
 * Differs from {@see ApiKeyDetail} — there is no `hash`, `name`, `disabled`,
 * or timestamp metadata, but extra `is_free_tier`/`is_management_key`/
 * `rate_limit` fields are present.
 *
 * @phpstan-type RateLimitType array{
 *     interval: string,
 *     requests: int,
 *     note: string,
 * }
 */
final class ApiKeyInfo
{
    /**
     * @param  RateLimitType|null  $rateLimit
     */
    private function __construct(
        public readonly string $label,
        public readonly float $limit,
        public readonly float $limitRemaining,
        public readonly ?string $limitReset,
        public readonly bool $includeByokInLimit,
        public readonly bool $isFreeTier,
        public readonly bool $isManagementKey,
        public readonly bool $isProvisioningKey,
        public readonly float $usage,
        public readonly float $usageDaily,
        public readonly float $usageWeekly,
        public readonly float $usageMonthly,
        public readonly float $byokUsage,
        public readonly float $byokUsageDaily,
        public readonly float $byokUsageWeekly,
        public readonly float $byokUsageMonthly,
        public readonly ?string $creatorUserId,
        public readonly ?string $expiresAt,
        public readonly ?array $rateLimit,
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

        $rateLimit = null;
        if (isset($attributes['rate_limit']) && is_array($attributes['rate_limit'])) {
            $rl = $attributes['rate_limit'];
            $rateLimit = [
                'interval' => is_string($rl['interval'] ?? null) ? $rl['interval'] : '',
                'requests' => is_int($rl['requests'] ?? null) ? $rl['requests'] : 0,
                'note' => is_string($rl['note'] ?? null) ? $rl['note'] : '',
            ];
        }

        return new self(
            label: $str($attributes['label'] ?? null),
            limit: $float($attributes['limit'] ?? null),
            limitRemaining: $float($attributes['limit_remaining'] ?? null),
            limitReset: $nullableStr($attributes['limit_reset'] ?? null),
            includeByokInLimit: (bool) ($attributes['include_byok_in_limit'] ?? false),
            isFreeTier: (bool) ($attributes['is_free_tier'] ?? false),
            isManagementKey: (bool) ($attributes['is_management_key'] ?? false),
            isProvisioningKey: (bool) ($attributes['is_provisioning_key'] ?? false),
            usage: $float($attributes['usage'] ?? null),
            usageDaily: $float($attributes['usage_daily'] ?? null),
            usageWeekly: $float($attributes['usage_weekly'] ?? null),
            usageMonthly: $float($attributes['usage_monthly'] ?? null),
            byokUsage: $float($attributes['byok_usage'] ?? null),
            byokUsageDaily: $float($attributes['byok_usage_daily'] ?? null),
            byokUsageWeekly: $float($attributes['byok_usage_weekly'] ?? null),
            byokUsageMonthly: $float($attributes['byok_usage_monthly'] ?? null),
            creatorUserId: $nullableStr($attributes['creator_user_id'] ?? null),
            expiresAt: $nullableStr($attributes['expires_at'] ?? null),
            rateLimit: $rateLimit,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'label' => $this->label,
            'limit' => $this->limit,
            'limit_remaining' => $this->limitRemaining,
            'limit_reset' => $this->limitReset,
            'include_byok_in_limit' => $this->includeByokInLimit,
            'is_free_tier' => $this->isFreeTier,
            'is_management_key' => $this->isManagementKey,
            'is_provisioning_key' => $this->isProvisioningKey,
            'usage' => $this->usage,
            'usage_daily' => $this->usageDaily,
            'usage_weekly' => $this->usageWeekly,
            'usage_monthly' => $this->usageMonthly,
            'byok_usage' => $this->byokUsage,
            'byok_usage_daily' => $this->byokUsageDaily,
            'byok_usage_weekly' => $this->byokUsageWeekly,
            'byok_usage_monthly' => $this->byokUsageMonthly,
            'creator_user_id' => $this->creatorUserId,
            'expires_at' => $this->expiresAt,
        ];

        if ($this->rateLimit !== null) {
            $data['rate_limit'] = $this->rateLimit;
        }

        return $data;
    }
}
