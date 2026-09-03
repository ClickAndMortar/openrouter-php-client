<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Observability;

/**
 * Typed builder for `PATCH /observability/destinations/{id}`. Every field is
 * optional; only the ones you set are sent. `type` cannot be changed after
 * creation, so it is absent here.
 */
final class UpdateObservabilityDestinationRequest
{
    /**
     * @param  array<string, mixed>|null  $config
     * @param  list<string>|null  $apiKeyHashes
     * @param  array<string, mixed>|null  $filterRules
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?array $config = null,
        public readonly ?bool $enabled = null,
        public readonly ?float $samplingRate = null,
        public readonly ?bool $privacyMode = null,
        public readonly ?bool $broadcastGenerationCost = null,
        public readonly ?bool $broadcastGenerationIdentity = null,
        public readonly ?bool $broadcastGenerationRequestContext = null,
        public readonly ?array $apiKeyHashes = null,
        public readonly ?array $filterRules = null,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ([
            'name' => $this->name,
            'config' => $this->config,
            'enabled' => $this->enabled,
            'sampling_rate' => $this->samplingRate,
            'privacy_mode' => $this->privacyMode,
            'broadcast_generation_cost' => $this->broadcastGenerationCost,
            'broadcast_generation_identity' => $this->broadcastGenerationIdentity,
            'broadcast_generation_request_context' => $this->broadcastGenerationRequestContext,
            'api_key_hashes' => $this->apiKeyHashes,
            'filter_rules' => $this->filterRules,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
