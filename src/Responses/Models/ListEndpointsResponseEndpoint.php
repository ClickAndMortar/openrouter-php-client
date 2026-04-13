<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

/**
 * @phpstan-import-type ListResponseModelPricingType from ListResponseModelPricing
 * @phpstan-import-type ListEndpointsResponsePercentileStatsType from ListEndpointsResponsePercentileStats
 *
 * @phpstan-type ListEndpointsResponseEndpointType array{
 *     name: string,
 *     model_id?: string,
 *     model_name: string,
 *     context_length: int,
 *     pricing: ListResponseModelPricingType,
 *     provider_name: string,
 *     tag: string,
 *     quantization?: string|null,
 *     max_completion_tokens?: int|null,
 *     max_prompt_tokens?: int|null,
 *     supported_parameters: array<int, string>,
 *     uptime_last_30m?: float|int|null,
 *     uptime_last_5m?: float|int|null,
 *     uptime_last_1d?: float|int|null,
 *     supports_implicit_caching?: bool,
 *     latency_last_30m?: ListEndpointsResponsePercentileStatsType|null,
 *     throughput_last_30m?: ListEndpointsResponsePercentileStatsType|null,
 *     status?: int|string|null,
 * }
 */
final class ListEndpointsResponseEndpoint
{
    /**
     * @param  array<int, string>  $supportedParameters
     */
    private function __construct(
        public readonly string $name,
        public readonly ?string $modelId,
        public readonly string $modelName,
        public readonly int $contextLength,
        public readonly ListResponseModelPricing $pricing,
        public readonly string $providerName,
        public readonly string $tag,
        public readonly ?string $quantization,
        public readonly ?int $maxCompletionTokens,
        public readonly ?int $maxPromptTokens,
        public readonly array $supportedParameters,
        public readonly float|int|null $uptimeLast30m,
        public readonly float|int|null $uptimeLast5m,
        public readonly float|int|null $uptimeLast1d,
        public readonly bool $supportsImplicitCaching,
        public readonly ?ListEndpointsResponsePercentileStats $latencyLast30m,
        public readonly ?ListEndpointsResponsePercentileStats $throughputLast30m,
        public readonly int|string|null $status,
    ) {
    }

    /**
     * @param  ListEndpointsResponseEndpointType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            name: $attributes['name'],
            modelId: $attributes['model_id'] ?? null,
            modelName: $attributes['model_name'],
            contextLength: $attributes['context_length'],
            pricing: ListResponseModelPricing::from($attributes['pricing']),
            providerName: $attributes['provider_name'],
            tag: $attributes['tag'],
            quantization: $attributes['quantization'] ?? null,
            maxCompletionTokens: $attributes['max_completion_tokens'] ?? null,
            maxPromptTokens: $attributes['max_prompt_tokens'] ?? null,
            supportedParameters: $attributes['supported_parameters'],
            uptimeLast30m: $attributes['uptime_last_30m'] ?? null,
            uptimeLast5m: $attributes['uptime_last_5m'] ?? null,
            uptimeLast1d: $attributes['uptime_last_1d'] ?? null,
            supportsImplicitCaching: $attributes['supports_implicit_caching'] ?? false,
            latencyLast30m: isset($attributes['latency_last_30m']) && is_array($attributes['latency_last_30m'])
                ? ListEndpointsResponsePercentileStats::from($attributes['latency_last_30m'])
                : null,
            throughputLast30m: isset($attributes['throughput_last_30m']) && is_array($attributes['throughput_last_30m'])
                ? ListEndpointsResponsePercentileStats::from($attributes['throughput_last_30m'])
                : null,
            status: $attributes['status'] ?? null,
        );
    }

    /**
     * @return ListEndpointsResponseEndpointType
     */
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'model_name' => $this->modelName,
            'context_length' => $this->contextLength,
            'pricing' => $this->pricing->toArray(),
            'provider_name' => $this->providerName,
            'tag' => $this->tag,
            'quantization' => $this->quantization,
            'max_completion_tokens' => $this->maxCompletionTokens,
            'max_prompt_tokens' => $this->maxPromptTokens,
            'supported_parameters' => $this->supportedParameters,
            'uptime_last_30m' => $this->uptimeLast30m,
            'uptime_last_5m' => $this->uptimeLast5m,
            'uptime_last_1d' => $this->uptimeLast1d,
            'supports_implicit_caching' => $this->supportsImplicitCaching,
            'latency_last_30m' => $this->latencyLast30m?->toArray(),
            'throughput_last_30m' => $this->throughputLast30m?->toArray(),
            'status' => $this->status,
        ];

        if ($this->modelId !== null) {
            $data['model_id'] = $this->modelId;
        }

        /** @var ListEndpointsResponseEndpointType $data */
        return $data;
    }
}
