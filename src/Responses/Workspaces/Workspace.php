<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Workspaces;

/**
 * A workspace: a scoped container for keys, guardrails, budgets and members.
 * Most account-level resources accept a `workspace_id` to operate inside one.
 */
final class Workspace
{
    /**
     * @param  list<string>|null  $ioLoggingApiKeyIds
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
        public readonly ?string $defaultGuardrailId,
        public readonly ?string $defaultTextModel,
        public readonly ?string $defaultImageModel,
        public readonly ?string $defaultProviderSort,
        public readonly ?bool $isObservabilityIoLoggingEnabled,
        public readonly ?bool $isObservabilityBroadcastEnabled,
        public readonly ?bool $isDataDiscountLoggingEnabled,
        public readonly ?float $ioLoggingSamplingRate,
        public readonly ?array $ioLoggingApiKeyIds,
        public readonly ?bool $includeByokInBudgets,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?string $createdBy,
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
            'name',
            'slug',
            'description',
            'default_guardrail_id',
            'default_text_model',
            'default_image_model',
            'default_provider_sort',
            'is_observability_io_logging_enabled',
            'is_observability_broadcast_enabled',
            'is_data_discount_logging_enabled',
            'io_logging_sampling_rate',
            'io_logging_api_key_ids',
            'include_byok_in_budgets',
            'created_at',
            'updated_at',
            'created_by',
        ]));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : '',
            slug: is_string($attributes['slug'] ?? null) ? $attributes['slug'] : '',
            description: is_string($attributes['description'] ?? null) ? $attributes['description'] : null,
            defaultGuardrailId: is_string($attributes['default_guardrail_id'] ?? null) ? $attributes['default_guardrail_id'] : null,
            defaultTextModel: is_string($attributes['default_text_model'] ?? null) ? $attributes['default_text_model'] : null,
            defaultImageModel: is_string($attributes['default_image_model'] ?? null) ? $attributes['default_image_model'] : null,
            defaultProviderSort: is_string($attributes['default_provider_sort'] ?? null) ? $attributes['default_provider_sort'] : null,
            isObservabilityIoLoggingEnabled: isset($attributes['is_observability_io_logging_enabled']) ? (bool) $attributes['is_observability_io_logging_enabled'] : null,
            isObservabilityBroadcastEnabled: isset($attributes['is_observability_broadcast_enabled']) ? (bool) $attributes['is_observability_broadcast_enabled'] : null,
            isDataDiscountLoggingEnabled: isset($attributes['is_data_discount_logging_enabled']) ? (bool) $attributes['is_data_discount_logging_enabled'] : null,
            ioLoggingSamplingRate: isset($attributes['io_logging_sampling_rate']) && is_numeric($attributes['io_logging_sampling_rate']) ? (float) $attributes['io_logging_sampling_rate'] : null,
            ioLoggingApiKeyIds: is_array($attributes['io_logging_api_key_ids'] ?? null)
                ? array_values(array_map('strval', $attributes['io_logging_api_key_ids']))
                : null,
            includeByokInBudgets: isset($attributes['include_byok_in_budgets']) ? (bool) $attributes['include_byok_in_budgets'] : null,
            createdAt: is_string($attributes['created_at'] ?? null) ? $attributes['created_at'] : null,
            updatedAt: is_string($attributes['updated_at'] ?? null) ? $attributes['updated_at'] : null,
            createdBy: is_string($attributes['created_by'] ?? null) ? $attributes['created_by'] : null,
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'default_guardrail_id' => $this->defaultGuardrailId,
            'default_text_model' => $this->defaultTextModel,
            'default_image_model' => $this->defaultImageModel,
            'default_provider_sort' => $this->defaultProviderSort,
            'is_observability_io_logging_enabled' => $this->isObservabilityIoLoggingEnabled,
            'is_observability_broadcast_enabled' => $this->isObservabilityBroadcastEnabled,
            'is_data_discount_logging_enabled' => $this->isDataDiscountLoggingEnabled,
            'io_logging_sampling_rate' => $this->ioLoggingSamplingRate,
            'io_logging_api_key_ids' => $this->ioLoggingApiKeyIds,
            'include_byok_in_budgets' => $this->includeByokInBudgets,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'created_by' => $this->createdBy,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
