<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Web-fetch plugin - lets the model retrieve URLs, bounded by domain lists,
 * a use count and a content-token budget.
 */
final class WebFetchPlugin implements Plugin
{
    /**
     * @param  list<string>|null  $allowedDomains
     * @param  list<string>|null  $blockedDomains
     */
    public function __construct(
        public readonly ?int $maxUses = null,
        public readonly ?int $maxContentTokens = null,
        public readonly ?array $allowedDomains = null,
        public readonly ?array $blockedDomains = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            maxUses: is_int($attributes['max_uses'] ?? null) ? $attributes['max_uses'] : null,
            maxContentTokens: is_int($attributes['max_content_tokens'] ?? null) ? $attributes['max_content_tokens'] : null,
            allowedDomains: isset($attributes['allowed_domains']) && is_array($attributes['allowed_domains'])
                ? array_values(array_map('strval', $attributes['allowed_domains']))
                : null,
            blockedDomains: isset($attributes['blocked_domains']) && is_array($attributes['blocked_domains'])
                ? array_values(array_map('strval', $attributes['blocked_domains']))
                : null,
        );
    }

    public function id(): string
    {
        return 'web-fetch';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['id' => $this->id()];

        if ($this->maxUses !== null) {
            $data['max_uses'] = $this->maxUses;
        }

        if ($this->maxContentTokens !== null) {
            $data['max_content_tokens'] = $this->maxContentTokens;
        }

        if ($this->allowedDomains !== null) {
            $data['allowed_domains'] = $this->allowedDomains;
        }

        if ($this->blockedDomains !== null) {
            $data['blocked_domains'] = $this->blockedDomains;
        }

        return $data;
    }
}
