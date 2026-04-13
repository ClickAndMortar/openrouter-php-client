<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Generation;

/**
 * @phpstan-type GenerationDataType array{
 *     id: string,
 *     upstream_id: ?string,
 *     total_cost: float,
 *     cache_discount: float,
 *     upstream_inference_cost: float,
 *     created_at: string,
 *     model: string,
 *     app_id: int,
 *     streamed: ?bool,
 *     cancelled: ?bool,
 *     provider_name: ?string,
 *     latency: float,
 *     moderation_latency: float,
 *     generation_time: float,
 *     finish_reason: ?string,
 *     tokens_prompt: int,
 *     tokens_completion: int,
 *     native_tokens_prompt: int,
 *     native_tokens_completion: int,
 *     native_tokens_completion_images: int,
 *     native_tokens_reasoning: int,
 *     native_tokens_cached: int,
 *     num_media_prompt: int,
 *     num_input_audio_prompt: int,
 *     num_media_completion: int,
 *     num_search_results: int,
 *     origin: string,
 *     usage: float,
 *     is_byok: bool,
 *     native_finish_reason: ?string,
 *     external_user: ?string,
 *     api_type: ?string,
 *     router: ?string,
 *     provider_responses: ?array<int, array<string, mixed>>,
 *     user_agent: ?string,
 *     http_referer: ?string,
 *     request_id?: ?string,
 *     session_id?: ?string,
 * }
 */
final class GenerationData
{
    /**
     * @param  array<int, array<string, mixed>>|null  $providerResponses
     */
    private function __construct(
        public readonly string $id,
        public readonly ?string $upstreamId,
        public readonly float $totalCost,
        public readonly float $cacheDiscount,
        public readonly float $upstreamInferenceCost,
        public readonly string $createdAt,
        public readonly string $model,
        public readonly int $appId,
        public readonly ?bool $streamed,
        public readonly ?bool $cancelled,
        public readonly ?string $providerName,
        public readonly float $latency,
        public readonly float $moderationLatency,
        public readonly float $generationTime,
        public readonly ?string $finishReason,
        public readonly int $tokensPrompt,
        public readonly int $tokensCompletion,
        public readonly int $nativeTokensPrompt,
        public readonly int $nativeTokensCompletion,
        public readonly int $nativeTokensCompletionImages,
        public readonly int $nativeTokensReasoning,
        public readonly int $nativeTokensCached,
        public readonly int $numMediaPrompt,
        public readonly int $numInputAudioPrompt,
        public readonly int $numMediaCompletion,
        public readonly int $numSearchResults,
        public readonly string $origin,
        public readonly float $usage,
        public readonly bool $isByok,
        public readonly ?string $nativeFinishReason,
        public readonly ?string $externalUser,
        public readonly ?string $apiType,
        public readonly ?string $router,
        public readonly ?array $providerResponses,
        public readonly ?string $userAgent,
        public readonly ?string $httpReferer,
        public readonly ?string $requestId,
        public readonly ?string $sessionId,
    ) {
    }

    /**
     * @param  GenerationDataType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: $attributes['id'],
            upstreamId: $attributes['upstream_id'],
            totalCost: (float) $attributes['total_cost'],
            cacheDiscount: (float) $attributes['cache_discount'],
            upstreamInferenceCost: (float) $attributes['upstream_inference_cost'],
            createdAt: $attributes['created_at'],
            model: $attributes['model'],
            appId: $attributes['app_id'],
            streamed: $attributes['streamed'],
            cancelled: $attributes['cancelled'],
            providerName: $attributes['provider_name'],
            latency: (float) $attributes['latency'],
            moderationLatency: (float) $attributes['moderation_latency'],
            generationTime: (float) $attributes['generation_time'],
            finishReason: $attributes['finish_reason'],
            tokensPrompt: $attributes['tokens_prompt'],
            tokensCompletion: $attributes['tokens_completion'],
            nativeTokensPrompt: $attributes['native_tokens_prompt'],
            nativeTokensCompletion: $attributes['native_tokens_completion'],
            nativeTokensCompletionImages: $attributes['native_tokens_completion_images'],
            nativeTokensReasoning: $attributes['native_tokens_reasoning'],
            nativeTokensCached: $attributes['native_tokens_cached'],
            numMediaPrompt: $attributes['num_media_prompt'],
            numInputAudioPrompt: $attributes['num_input_audio_prompt'],
            numMediaCompletion: $attributes['num_media_completion'],
            numSearchResults: $attributes['num_search_results'],
            origin: $attributes['origin'],
            usage: (float) $attributes['usage'],
            isByok: $attributes['is_byok'],
            nativeFinishReason: $attributes['native_finish_reason'],
            externalUser: $attributes['external_user'],
            apiType: $attributes['api_type'],
            router: $attributes['router'],
            providerResponses: $attributes['provider_responses'],
            userAgent: $attributes['user_agent'],
            httpReferer: $attributes['http_referer'],
            requestId: $attributes['request_id'] ?? null,
            sessionId: $attributes['session_id'] ?? null,
        );
    }

    /**
     * @return GenerationDataType
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'upstream_id' => $this->upstreamId,
            'total_cost' => $this->totalCost,
            'cache_discount' => $this->cacheDiscount,
            'upstream_inference_cost' => $this->upstreamInferenceCost,
            'created_at' => $this->createdAt,
            'model' => $this->model,
            'app_id' => $this->appId,
            'streamed' => $this->streamed,
            'cancelled' => $this->cancelled,
            'provider_name' => $this->providerName,
            'latency' => $this->latency,
            'moderation_latency' => $this->moderationLatency,
            'generation_time' => $this->generationTime,
            'finish_reason' => $this->finishReason,
            'tokens_prompt' => $this->tokensPrompt,
            'tokens_completion' => $this->tokensCompletion,
            'native_tokens_prompt' => $this->nativeTokensPrompt,
            'native_tokens_completion' => $this->nativeTokensCompletion,
            'native_tokens_completion_images' => $this->nativeTokensCompletionImages,
            'native_tokens_reasoning' => $this->nativeTokensReasoning,
            'native_tokens_cached' => $this->nativeTokensCached,
            'num_media_prompt' => $this->numMediaPrompt,
            'num_input_audio_prompt' => $this->numInputAudioPrompt,
            'num_media_completion' => $this->numMediaCompletion,
            'num_search_results' => $this->numSearchResults,
            'origin' => $this->origin,
            'usage' => $this->usage,
            'is_byok' => $this->isByok,
            'native_finish_reason' => $this->nativeFinishReason,
            'external_user' => $this->externalUser,
            'api_type' => $this->apiType,
            'router' => $this->router,
            'provider_responses' => $this->providerResponses,
            'user_agent' => $this->userAgent,
            'http_referer' => $this->httpReferer,
            'request_id' => $this->requestId,
            'session_id' => $this->sessionId,
        ];
    }
}
