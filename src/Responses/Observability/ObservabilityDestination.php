<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Observability;

/**
 * A sink that OpenRouter broadcasts generation telemetry to.
 *
 * The API defines seventeen destination types (Langfuse, Datadog, S3, a plain
 * webhook and so on). They share this envelope and differ only in `type` and
 * the shape of `config`, which is therefore kept as a raw array rather than
 * modelled seventeen times.
 */
final class ObservabilityDestination
{
    /**
     * @param  array<string, mixed>|null  $config
     * @param  list<string>|null  $apiKeyHashes
     * @param  array<string, mixed>|null  $filterRules
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly ?string $name,
        public readonly ?bool $enabled,
        public readonly ?array $config,
        public readonly ?float $samplingRate,
        public readonly ?bool $privacyMode,
        public readonly ?bool $broadcastGenerationCost,
        public readonly ?bool $broadcastGenerationIdentity,
        public readonly ?bool $broadcastGenerationRequestContext,
        public readonly ?array $apiKeyHashes,
        public readonly ?array $filterRules,
        public readonly ?string $workspaceId,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'id',
            'type',
            'name',
            'enabled',
            'config',
            'sampling_rate',
            'privacy_mode',
            'broadcast_generation_cost',
            'broadcast_generation_identity',
            'broadcast_generation_request_context',
            'api_key_hashes',
            'filter_rules',
            'workspace_id',
            'created_at',
            'updated_at',
        ]));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            type: is_string($attributes['type'] ?? null) ? $attributes['type'] : '',
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : null,
            enabled: isset($attributes['enabled']) ? (bool) $attributes['enabled'] : null,
            config: is_array($attributes['config'] ?? null) ? $attributes['config'] : null,
            samplingRate: isset($attributes['sampling_rate']) && is_numeric($attributes['sampling_rate']) ? (float) $attributes['sampling_rate'] : null,
            privacyMode: isset($attributes['privacy_mode']) ? (bool) $attributes['privacy_mode'] : null,
            broadcastGenerationCost: isset($attributes['broadcast_generation_cost']) ? (bool) $attributes['broadcast_generation_cost'] : null,
            broadcastGenerationIdentity: isset($attributes['broadcast_generation_identity']) ? (bool) $attributes['broadcast_generation_identity'] : null,
            broadcastGenerationRequestContext: isset($attributes['broadcast_generation_request_context']) ? (bool) $attributes['broadcast_generation_request_context'] : null,
            apiKeyHashes: is_array($attributes['api_key_hashes'] ?? null)
                ? array_values(array_map('strval', $attributes['api_key_hashes']))
                : null,
            filterRules: is_array($attributes['filter_rules'] ?? null) ? $attributes['filter_rules'] : null,
            workspaceId: is_string($attributes['workspace_id'] ?? null) ? $attributes['workspace_id'] : null,
            createdAt: is_string($attributes['created_at'] ?? null) ? $attributes['created_at'] : null,
            updatedAt: is_string($attributes['updated_at'] ?? null) ? $attributes['updated_at'] : null,
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
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'enabled' => $this->enabled,
            'config' => $this->config,
            'sampling_rate' => $this->samplingRate,
            'privacy_mode' => $this->privacyMode,
            'broadcast_generation_cost' => $this->broadcastGenerationCost,
            'broadcast_generation_identity' => $this->broadcastGenerationIdentity,
            'broadcast_generation_request_context' => $this->broadcastGenerationRequestContext,
            'api_key_hashes' => $this->apiKeyHashes,
            'filter_rules' => $this->filterRules,
            'workspace_id' => $this->workspaceId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
