<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Observability;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for `POST /observability/destinations`.
 *
 * `$type` names one of the seventeen supported sinks (`langfuse`, `datadog`,
 * `s3`, `webhook`, ...) and `$config` carries whatever that sink requires, so
 * it stays a raw array.
 */
final class CreateObservabilityDestinationRequest
{
    /**
     * @param  array<string, mixed>  $config
     * @param  list<string>|null  $apiKeyHashes
     * @param  array<string, mixed>|null  $filterRules
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $type,
        public readonly string $name,
        public readonly array $config,
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
        if ($type === '') {
            throw new InvalidArgumentException('CreateObservabilityDestinationRequest::$type must not be empty');
        }

        if ($name === '') {
            throw new InvalidArgumentException('CreateObservabilityDestinationRequest::$name must not be empty');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type, 'name' => $this->name, 'config' => $this->config];

        foreach ([
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
