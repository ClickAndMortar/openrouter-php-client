<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Activity;

/**
 * @phpstan-type ActivityItemType array{
 *     date: string,
 *     model: string,
 *     model_permaslug: string,
 *     endpoint_id: string,
 *     provider_name: string,
 *     usage: float|int,
 *     byok_usage_inference: float|int,
 *     requests: int,
 *     prompt_tokens: int,
 *     completion_tokens: int,
 *     reasoning_tokens: int,
 * }
 */
final class ActivityItem
{
    private function __construct(
        public readonly string $date,
        public readonly string $model,
        public readonly string $modelPermaslug,
        public readonly string $endpointId,
        public readonly string $providerName,
        public readonly float $usage,
        public readonly float $byokUsageInference,
        public readonly int $requests,
        public readonly int $promptTokens,
        public readonly int $completionTokens,
        public readonly int $reasoningTokens,
    ) {
    }

    /**
     * @param  ActivityItemType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            date: $attributes['date'],
            model: $attributes['model'],
            modelPermaslug: $attributes['model_permaslug'],
            endpointId: $attributes['endpoint_id'],
            providerName: $attributes['provider_name'],
            usage: (float) $attributes['usage'],
            byokUsageInference: (float) $attributes['byok_usage_inference'],
            requests: $attributes['requests'],
            promptTokens: $attributes['prompt_tokens'],
            completionTokens: $attributes['completion_tokens'],
            reasoningTokens: $attributes['reasoning_tokens'],
        );
    }

    /**
     * @return ActivityItemType
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'model' => $this->model,
            'model_permaslug' => $this->modelPermaslug,
            'endpoint_id' => $this->endpointId,
            'provider_name' => $this->providerName,
            'usage' => $this->usage,
            'byok_usage_inference' => $this->byokUsageInference,
            'requests' => $this->requests,
            'prompt_tokens' => $this->promptTokens,
            'completion_tokens' => $this->completionTokens,
            'reasoning_tokens' => $this->reasoningTokens,
        ];
    }
}
