<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Workspaces;

/**
 * Typed builder for `PATCH /workspaces/{id}`. Every field is optional; only
 * the ones you set are sent, so an unset field is left untouched upstream.
 */
final class UpdateWorkspaceRequest
{
    /**
     * @param  list<string>|null  $ioLoggingApiKeyIds
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $slug = null,
        public readonly ?string $description = null,
        public readonly ?string $defaultTextModel = null,
        public readonly ?string $defaultImageModel = null,
        public readonly ?string $defaultProviderSort = null,
        public readonly ?bool $isObservabilityIoLoggingEnabled = null,
        public readonly ?bool $isObservabilityBroadcastEnabled = null,
        public readonly ?bool $isDataDiscountLoggingEnabled = null,
        public readonly ?float $ioLoggingSamplingRate = null,
        public readonly ?array $ioLoggingApiKeyIds = null,
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
            'slug' => $this->slug,
            'description' => $this->description,
            'default_text_model' => $this->defaultTextModel,
            'default_image_model' => $this->defaultImageModel,
            'default_provider_sort' => $this->defaultProviderSort,
            'is_observability_io_logging_enabled' => $this->isObservabilityIoLoggingEnabled,
            'is_observability_broadcast_enabled' => $this->isObservabilityBroadcastEnabled,
            'is_data_discount_logging_enabled' => $this->isDataDiscountLoggingEnabled,
            'io_logging_sampling_rate' => $this->ioLoggingSamplingRate,
            'io_logging_api_key_ids' => $this->ioLoggingApiKeyIds,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
