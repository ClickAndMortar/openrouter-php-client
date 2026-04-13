<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat;

use OpenRouter\Enums\Responses\OutputModality;
use OpenRouter\Enums\Responses\ServiceTier;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Chat\Config\ChatDebugOptions;
use OpenRouter\ValueObjects\Chat\Config\ChatReasoningConfig;
use OpenRouter\ValueObjects\Chat\Config\ChatStreamOptions;
use OpenRouter\ValueObjects\Chat\Config\ChatToolChoice;
use OpenRouter\ValueObjects\Chat\Config\ResponseFormat;
use OpenRouter\ValueObjects\Chat\Content\ChatCacheControl;
use OpenRouter\ValueObjects\Chat\Messages\ChatMessage;
use OpenRouter\ValueObjects\Chat\Tools\ChatTool;
use OpenRouter\ValueObjects\Responses\Config\ProviderPreferences;
use OpenRouter\ValueObjects\Responses\Config\TraceConfig;
use OpenRouter\ValueObjects\Responses\Plugins\Plugin;

/**
 * Typed builder for a `POST /chat/completions` request. Mirrors the
 * `ChatRequest` schema. Validates that `messages` is non-empty at construction
 * time and serializes via {@see toArray()}.
 *
 * Every typed field accepts either the matching VO or a raw array, so callers
 * can mix typed builders with passthrough arrays. Unknown fields pass through
 * `$extras` unchanged.
 */
final class CreateChatRequest
{
    private const IDENTIFIER_MAX_LENGTH = 256;

    private const METADATA_MAX_ENTRIES = 16;

    private const METADATA_KEY_MAX_LENGTH = 64;

    private const METADATA_VALUE_MAX_LENGTH = 512;

    /**
     * @param  list<ChatMessage|array<string, mixed>>  $messages
     * @param  list<string>|null  $models
     * @param  list<ChatTool|array<string, mixed>>|null  $tools
     * @param  list<Plugin|array<string, mixed>>|null  $plugins
     * @param  ChatToolChoice|string|array<string, mixed>|null  $toolChoice
     * @param  ChatReasoningConfig|array<string, mixed>|null  $reasoning
     * @param  ResponseFormat|array<string, mixed>|null  $responseFormat
     * @param  ProviderPreferences|array<string, mixed>|null  $provider
     * @param  TraceConfig|array<string, mixed>|null  $trace
     * @param  ChatStreamOptions|array<string, mixed>|null  $streamOptions
     * @param  ChatDebugOptions|array<string, mixed>|null  $debug
     * @param  ChatCacheControl|array<string, mixed>|null  $cacheControl
     * @param  list<OutputModality|string>|null  $modalities
     * @param  array<string, mixed>|null  $logitBias
     * @param  array<string, mixed>|null  $imageConfig
     * @param  array<string, mixed>|null  $metadata
     * @param  string|list<string>|null  $stop
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly array $messages,
        public readonly ?string $model = null,
        public readonly ?array $models = null,
        public readonly ?bool $stream = null,
        public readonly mixed $streamOptions = null,
        public readonly ?float $temperature = null,
        public readonly ?float $topP = null,
        public readonly ?int $topLogprobs = null,
        public readonly ?bool $logprobs = null,
        public readonly ?array $logitBias = null,
        public readonly ?int $maxTokens = null,
        public readonly ?int $maxCompletionTokens = null,
        public readonly ?float $frequencyPenalty = null,
        public readonly ?float $presencePenalty = null,
        public readonly ?int $seed = null,
        public readonly mixed $stop = null,
        public readonly ?string $user = null,
        public readonly ?array $tools = null,
        public readonly mixed $toolChoice = null,
        public readonly ?bool $parallelToolCalls = null,
        public readonly mixed $responseFormat = null,
        public readonly mixed $reasoning = null,
        public readonly ?array $modalities = null,
        public readonly ?array $imageConfig = null,
        public readonly ?array $metadata = null,
        public readonly ?ServiceTier $serviceTier = null,
        public readonly ?string $sessionId = null,
        public readonly mixed $cacheControl = null,
        public readonly mixed $debug = null,
        public readonly mixed $provider = null,
        public readonly ?array $plugins = null,
        public readonly mixed $trace = null,
        public readonly ?string $route = null,
        public readonly array $extras = [],
    ) {
        if ($this->messages === []) {
            throw new InvalidArgumentException('CreateChatRequest::$messages must not be empty');
        }

        if ($this->model !== null && $this->model === '') {
            throw new InvalidArgumentException('CreateChatRequest::$model must not be an empty string');
        }

        if (is_array($this->models) && $this->models === []) {
            throw new InvalidArgumentException('CreateChatRequest::$models must not be an empty array');
        }

        if ($this->topLogprobs !== null && ($this->topLogprobs < 0 || $this->topLogprobs > 20)) {
            throw new InvalidArgumentException('CreateChatRequest::$topLogprobs must be between 0 and 20');
        }

        if ($this->frequencyPenalty !== null && ! is_finite($this->frequencyPenalty)) {
            throw new InvalidArgumentException('CreateChatRequest::$frequencyPenalty must be a finite number');
        }

        if ($this->presencePenalty !== null && ! is_finite($this->presencePenalty)) {
            throw new InvalidArgumentException('CreateChatRequest::$presencePenalty must be a finite number');
        }

        if (is_array($this->stop) && count($this->stop) > 4) {
            throw new InvalidArgumentException('CreateChatRequest::$stop must contain at most 4 entries');
        }

        $this->validateIdentifier('user', $this->user);
        $this->validateIdentifier('sessionId', $this->sessionId);

        if ($this->metadata !== null) {
            $this->validateMetadata($this->metadata);
        }

        if ($this->modalities !== null && $this->modalities === []) {
            throw new InvalidArgumentException('CreateChatRequest::$modalities must not be an empty array');
        }
    }

    private function validateIdentifier(string $field, ?string $value): void
    {
        if ($value === null) {
            return;
        }

        if (mb_strlen($value) > self::IDENTIFIER_MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf(
                'CreateChatRequest::$%s must be <= %d characters',
                $field,
                self::IDENTIFIER_MAX_LENGTH,
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    private function validateMetadata(array $metadata): void
    {
        if (count($metadata) > self::METADATA_MAX_ENTRIES) {
            throw new InvalidArgumentException(sprintf(
                'CreateChatRequest::$metadata must contain at most %d entries',
                self::METADATA_MAX_ENTRIES,
            ));
        }

        foreach ($metadata as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('CreateChatRequest::$metadata keys must be strings');
            }

            if (mb_strlen($key) > self::METADATA_KEY_MAX_LENGTH) {
                throw new InvalidArgumentException(sprintf(
                    'CreateChatRequest::$metadata key "%s" exceeds %d characters',
                    $key,
                    self::METADATA_KEY_MAX_LENGTH,
                ));
            }

            if (! is_string($value)) {
                throw new InvalidArgumentException(sprintf(
                    'CreateChatRequest::$metadata["%s"] must be a string',
                    $key,
                ));
            }

            if (mb_strlen($value) > self::METADATA_VALUE_MAX_LENGTH) {
                throw new InvalidArgumentException(sprintf(
                    'CreateChatRequest::$metadata["%s"] exceeds %d characters',
                    $key,
                    self::METADATA_VALUE_MAX_LENGTH,
                ));
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'messages' => array_map(
                static fn (ChatMessage|array $m): array => $m instanceof ChatMessage ? $m->toArray() : $m,
                $this->messages,
            ),
        ];

        if ($this->model !== null) {
            $data['model'] = $this->model;
        }

        if ($this->models !== null) {
            $data['models'] = $this->models;
        }

        if ($this->tools !== null) {
            $data['tools'] = array_map(
                static fn (ChatTool|array $tool): array => $tool instanceof ChatTool ? $tool->toArray() : $tool,
                $this->tools,
            );
        }

        if ($this->plugins !== null) {
            $data['plugins'] = array_map(
                static fn (Plugin|array $plugin): array => $plugin instanceof Plugin ? $plugin->toArray() : $plugin,
                $this->plugins,
            );
        }

        if ($this->toolChoice !== null) {
            $data['tool_choice'] = $this->toolChoice instanceof ChatToolChoice
                ? $this->toolChoice->toArray()
                : $this->toolChoice;
        }

        if ($this->responseFormat !== null) {
            $data['response_format'] = $this->responseFormat instanceof ResponseFormat
                ? $this->responseFormat->toArray()
                : $this->responseFormat;
        }

        if ($this->reasoning !== null) {
            $data['reasoning'] = $this->reasoning instanceof ChatReasoningConfig
                ? $this->reasoning->toArray()
                : $this->reasoning;
        }

        if ($this->provider !== null) {
            $data['provider'] = $this->provider instanceof ProviderPreferences
                ? $this->provider->toArray()
                : $this->provider;
        }

        if ($this->trace !== null) {
            $data['trace'] = $this->trace instanceof TraceConfig
                ? $this->trace->toArray()
                : $this->trace;
        }

        if ($this->streamOptions !== null) {
            $data['stream_options'] = $this->streamOptions instanceof ChatStreamOptions
                ? $this->streamOptions->toArray()
                : $this->streamOptions;
        }

        if ($this->debug !== null) {
            $data['debug'] = $this->debug instanceof ChatDebugOptions
                ? $this->debug->toArray()
                : $this->debug;
        }

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl instanceof ChatCacheControl
                ? $this->cacheControl->toArray()
                : $this->cacheControl;
        }

        if ($this->modalities !== null) {
            $data['modalities'] = array_values(array_map(
                static fn (OutputModality|string $m): string => $m instanceof OutputModality ? $m->value : $m,
                $this->modalities,
            ));
        }

        if ($this->serviceTier !== null) {
            $data['service_tier'] = $this->serviceTier->value;
        }

        if ($this->stop !== null) {
            $data['stop'] = $this->stop;
        }

        $optional = [
            'stream' => $this->stream,
            'temperature' => $this->temperature,
            'top_p' => $this->topP,
            'top_logprobs' => $this->topLogprobs,
            'logprobs' => $this->logprobs,
            'logit_bias' => $this->logitBias,
            'max_tokens' => $this->maxTokens,
            'max_completion_tokens' => $this->maxCompletionTokens,
            'frequency_penalty' => $this->frequencyPenalty,
            'presence_penalty' => $this->presencePenalty,
            'seed' => $this->seed,
            'user' => $this->user,
            'parallel_tool_calls' => $this->parallelToolCalls,
            'image_config' => $this->imageConfig,
            'metadata' => $this->metadata,
            'session_id' => $this->sessionId,
            'route' => $this->route,
        ];

        foreach ($optional as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
