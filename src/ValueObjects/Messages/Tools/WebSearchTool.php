<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Tools;

use OpenRouter\ValueObjects\Messages\Content\MessagesCacheControl;

/**
 * Anthropic web-search tool. Two dated variants are supported:
 * `web_search_20250305` (original) and `web_search_20260209` (adds
 * `allowed_callers`). `$version` defaults to the latest.
 */
final class WebSearchTool implements MessagesTool
{
    public const VERSION_2025_03_05 = 'web_search_20250305';

    public const VERSION_2026_02_09 = 'web_search_20260209';

    public const VERSIONS = [self::VERSION_2025_03_05, self::VERSION_2026_02_09];

    /**
     * @param  list<string>|null  $allowedDomains
     * @param  list<string>|null  $blockedDomains
     * @param  list<string>|null  $allowedCallers
     * @param  array<string, mixed>|null  $userLocation
     */
    public function __construct(
        public readonly string $version = self::VERSION_2026_02_09,
        public readonly ?int $maxUses = null,
        public readonly ?array $allowedDomains = null,
        public readonly ?array $blockedDomains = null,
        public readonly ?array $allowedCallers = null,
        public readonly ?array $userLocation = null,
        public readonly ?MessagesCacheControl $cacheControl = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : self::VERSION_2026_02_09;

        return new self(
            version: in_array($type, self::VERSIONS, true) ? $type : self::VERSION_2026_02_09,
            maxUses: isset($attributes['max_uses']) && is_int($attributes['max_uses']) ? $attributes['max_uses'] : null,
            allowedDomains: isset($attributes['allowed_domains']) && is_array($attributes['allowed_domains'])
                ? array_values(array_filter($attributes['allowed_domains'], 'is_string'))
                : null,
            blockedDomains: isset($attributes['blocked_domains']) && is_array($attributes['blocked_domains'])
                ? array_values(array_filter($attributes['blocked_domains'], 'is_string'))
                : null,
            allowedCallers: isset($attributes['allowed_callers']) && is_array($attributes['allowed_callers'])
                ? array_values(array_filter($attributes['allowed_callers'], 'is_string'))
                : null,
            userLocation: isset($attributes['user_location']) && is_array($attributes['user_location'])
                ? $attributes['user_location']
                : null,
            cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                ? MessagesCacheControl::from($attributes['cache_control'])
                : null,
        );
    }

    public function type(): string
    {
        return $this->version;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'name' => 'web_search',
        ];

        $optional = [
            'max_uses' => $this->maxUses,
            'allowed_domains' => $this->allowedDomains,
            'blocked_domains' => $this->blockedDomains,
            'user_location' => $this->userLocation,
        ];

        foreach ($optional as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        if ($this->version === self::VERSION_2026_02_09 && $this->allowedCallers !== null) {
            $data['allowed_callers'] = $this->allowedCallers;
        }

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
