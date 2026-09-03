<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Workspaces;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for `POST /workspaces`.
 */
final class CreateWorkspaceRequest
{
    /**
     * @param  list<string>|null  $ioLoggingApiKeyIds
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
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
        if ($name === '') {
            throw new InvalidArgumentException('CreateWorkspaceRequest::$name must not be empty');
        }

        if ($slug === '') {
            throw new InvalidArgumentException('CreateWorkspaceRequest::$slug must not be empty');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['name' => $this->name, 'slug' => $this->slug];

        foreach (self::optional($this) as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }

    /**
     * @return array<string, mixed>
     */
    private static function optional(self $r): array
    {
        return [
            'description' => $r->description,
            'default_text_model' => $r->defaultTextModel,
            'default_image_model' => $r->defaultImageModel,
            'default_provider_sort' => $r->defaultProviderSort,
            'is_observability_io_logging_enabled' => $r->isObservabilityIoLoggingEnabled,
            'is_observability_broadcast_enabled' => $r->isObservabilityBroadcastEnabled,
            'is_data_discount_logging_enabled' => $r->isDataDiscountLoggingEnabled,
            'io_logging_sampling_rate' => $r->ioLoggingSamplingRate,
            'io_logging_api_key_ids' => $r->ioLoggingApiKeyIds,
        ];
    }
}
