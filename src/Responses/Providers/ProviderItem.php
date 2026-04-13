<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Providers;

/**
 * @phpstan-type ProviderItemType array{
 *     slug: string,
 *     name: string,
 *     headquarters?: ?string,
 *     datacenters?: ?array<int, string>,
 *     privacy_policy_url?: ?string,
 *     terms_of_service_url?: ?string,
 *     status_page_url?: ?string,
 * }
 */
final class ProviderItem
{
    /**
     * @param  array<int, string>|null  $datacenters
     */
    private function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly ?string $headquarters,
        public readonly ?array $datacenters,
        public readonly ?string $privacyPolicyUrl,
        public readonly ?string $termsOfServiceUrl,
        public readonly ?string $statusPageUrl,
    ) {
    }

    /**
     * @param  ProviderItemType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            slug: $attributes['slug'],
            name: $attributes['name'],
            headquarters: $attributes['headquarters'] ?? null,
            datacenters: $attributes['datacenters'] ?? null,
            privacyPolicyUrl: $attributes['privacy_policy_url'] ?? null,
            termsOfServiceUrl: $attributes['terms_of_service_url'] ?? null,
            statusPageUrl: $attributes['status_page_url'] ?? null,
        );
    }

    /**
     * @return ProviderItemType
     */
    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'headquarters' => $this->headquarters,
            'datacenters' => $this->datacenters,
            'privacy_policy_url' => $this->privacyPolicyUrl,
            'terms_of_service_url' => $this->termsOfServiceUrl,
            'status_page_url' => $this->statusPageUrl,
        ];
    }
}
