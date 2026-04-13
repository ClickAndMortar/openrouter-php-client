<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses;

use OpenRouter\Enums\Responses\OutputModality;
use OpenRouter\Enums\Responses\ResponseIncludes;
use OpenRouter\Enums\Responses\ServiceTier;
use OpenRouter\Enums\Responses\Truncation;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Config\ProviderPreferences;
use OpenRouter\ValueObjects\Responses\Config\ReasoningConfig;
use OpenRouter\ValueObjects\Responses\Config\StoredPromptTemplate;
use OpenRouter\ValueObjects\Responses\Config\TextExtendedConfig;
use OpenRouter\ValueObjects\Responses\Config\ToolChoice;
use OpenRouter\ValueObjects\Responses\Config\TraceConfig;
use OpenRouter\ValueObjects\Responses\Input\InputItem;
use OpenRouter\ValueObjects\Responses\Plugins\Plugin;
use OpenRouter\ValueObjects\Responses\Tools\Tool;

/**
 * Typed builder for a `POST /responses` request. Validates the bare minimum
 * (`model` is non-empty, `input` is non-empty) at construction time and
 * serializes to the array shape expected by the transporter via {@see toArray()}.
 *
 * Every typed field accepts either the matching VO or a raw array, so callers
 * can mix typed builders with passthrough arrays for fields they don't want
 * to type. Unknown fields pass through `$extras` unchanged.
 */
final class CreateResponseRequest
{
    private const IDENTIFIER_MAX_LENGTH = 256;

    private const METADATA_MAX_ENTRIES = 16;

    private const METADATA_KEY_MAX_LENGTH = 64;

    private const METADATA_VALUE_MAX_LENGTH = 512;

    /**
     * @param  string|list<InputItem|array<string, mixed>>  $input
     * @param  list<Tool|array<string, mixed>>|null  $tools
     * @param  list<Plugin|array<string, mixed>>|null  $plugins
     * @param  ToolChoice|string|array<string, mixed>|null  $toolChoice
     * @param  ReasoningConfig|array<string, mixed>|null  $reasoning
     * @param  TextExtendedConfig|array<string, mixed>|null  $text
     * @param  ProviderPreferences|array<string, mixed>|null  $provider
     * @param  TraceConfig|array<string, mixed>|null  $trace
     * @param  StoredPromptTemplate|array<string, mixed>|null  $prompt
     * @param  list<ResponseIncludes>|null  $include
     * @param  list<string>|null  $models
     * @param  list<OutputModality>|null  $modalities
     * @param  array<string, mixed>|null  $imageConfig
     * @param  array<string, mixed>|null  $metadata
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $model,
        public readonly string|array $input,
        public readonly ?array $tools = null,
        public readonly ?array $plugins = null,
        public readonly mixed $toolChoice = null,
        public readonly mixed $reasoning = null,
        public readonly mixed $text = null,
        public readonly mixed $provider = null,
        public readonly mixed $trace = null,
        public readonly mixed $prompt = null,
        public readonly ?array $include = null,
        public readonly ?Truncation $truncation = null,
        public readonly ?array $models = null,
        public readonly ?bool $parallelToolCalls = null,
        public readonly ?float $temperature = null,
        public readonly ?float $topP = null,
        public readonly ?int $maxOutputTokens = null,
        public readonly ?string $instructions = null,
        public readonly ?string $previousResponseId = null,
        public readonly ?bool $stream = null,
        public readonly ?array $metadata = null,
        public readonly ?array $modalities = null,
        public readonly ?array $imageConfig = null,
        public readonly ?bool $background = null,
        public readonly ?ServiceTier $serviceTier = null,
        public readonly ?string $safetyIdentifier = null,
        public readonly ?string $sessionId = null,
        public readonly ?string $user = null,
        public readonly ?float $frequencyPenalty = null,
        public readonly ?float $presencePenalty = null,
        public readonly ?int $topK = null,
        public readonly ?int $topLogprobs = null,
        public readonly ?string $promptCacheKey = null,
        public readonly ?int $maxToolCalls = null,
        public readonly array $extras = [],
    ) {
        if ($this->model === '') {
            throw new InvalidArgumentException('CreateResponseRequest::$model must not be empty');
        }

        if (is_string($this->input) && $this->input === '') {
            throw new InvalidArgumentException('CreateResponseRequest::$input must not be empty');
        }

        if (is_array($this->input) && $this->input === []) {
            throw new InvalidArgumentException('CreateResponseRequest::$input must not be an empty array');
        }

        if ($this->include !== null) {
            foreach ($this->include as $entry) {
                if (! $entry instanceof ResponseIncludes) {
                    throw new InvalidArgumentException(
                        'CreateResponseRequest::$include entries must be ResponseIncludes instances',
                    );
                }
            }
        }

        if (is_array($this->models) && $this->models === []) {
            throw new InvalidArgumentException('CreateResponseRequest::$models must not be an empty array');
        }

        if ($this->modalities !== null) {
            if ($this->modalities === []) {
                throw new InvalidArgumentException('CreateResponseRequest::$modalities must not be an empty array');
            }
            foreach ($this->modalities as $entry) {
                if (! $entry instanceof OutputModality) {
                    throw new InvalidArgumentException(
                        'CreateResponseRequest::$modalities entries must be OutputModality instances',
                    );
                }
            }
        }

        $this->validateIdentifier('safetyIdentifier', $this->safetyIdentifier);
        $this->validateIdentifier('sessionId', $this->sessionId);
        $this->validateIdentifier('user', $this->user);
        $this->validateIdentifier('promptCacheKey', $this->promptCacheKey);

        if ($this->frequencyPenalty !== null && ! is_finite($this->frequencyPenalty)) {
            throw new InvalidArgumentException('CreateResponseRequest::$frequencyPenalty must be a finite number');
        }

        if ($this->presencePenalty !== null && ! is_finite($this->presencePenalty)) {
            throw new InvalidArgumentException('CreateResponseRequest::$presencePenalty must be a finite number');
        }

        if ($this->topK !== null && $this->topK < 0) {
            throw new InvalidArgumentException('CreateResponseRequest::$topK must be >= 0');
        }

        if ($this->topLogprobs !== null && $this->topLogprobs < 0) {
            throw new InvalidArgumentException('CreateResponseRequest::$topLogprobs must be >= 0');
        }

        if ($this->maxToolCalls !== null && $this->maxToolCalls < 0) {
            throw new InvalidArgumentException('CreateResponseRequest::$maxToolCalls must be >= 0');
        }

        if ($this->metadata !== null) {
            $this->validateMetadata($this->metadata);
        }
    }

    private function validateIdentifier(string $field, ?string $value): void
    {
        if ($value === null) {
            return;
        }

        if (mb_strlen($value) > self::IDENTIFIER_MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf(
                'CreateResponseRequest::$%s must be <= %d characters',
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
                'CreateResponseRequest::$metadata must contain at most %d entries',
                self::METADATA_MAX_ENTRIES,
            ));
        }

        foreach ($metadata as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('CreateResponseRequest::$metadata keys must be strings');
            }

            if (mb_strlen($key) > self::METADATA_KEY_MAX_LENGTH) {
                throw new InvalidArgumentException(sprintf(
                    'CreateResponseRequest::$metadata key "%s" exceeds %d characters',
                    $key,
                    self::METADATA_KEY_MAX_LENGTH,
                ));
            }

            if (str_contains($key, '[') || str_contains($key, ']')) {
                throw new InvalidArgumentException(sprintf(
                    'CreateResponseRequest::$metadata key "%s" must not contain "[" or "]"',
                    $key,
                ));
            }

            if (! is_string($value)) {
                throw new InvalidArgumentException(sprintf(
                    'CreateResponseRequest::$metadata["%s"] must be a string',
                    $key,
                ));
            }

            if (mb_strlen($value) > self::METADATA_VALUE_MAX_LENGTH) {
                throw new InvalidArgumentException(sprintf(
                    'CreateResponseRequest::$metadata["%s"] exceeds %d characters',
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
            'model' => $this->model,
            'input' => is_string($this->input)
                ? $this->input
                : array_map(
                    static fn (InputItem|array $item): array => $item instanceof InputItem ? $item->toArray() : $item,
                    $this->input,
                ),
        ];

        if ($this->tools !== null) {
            $data['tools'] = array_map(
                static fn (Tool|array $tool): array => $tool instanceof Tool ? $tool->toArray() : $tool,
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
            $data['tool_choice'] = $this->toolChoice instanceof ToolChoice
                ? $this->toolChoice->toArray()
                : $this->toolChoice;
        }

        if ($this->reasoning !== null) {
            $data['reasoning'] = $this->reasoning instanceof ReasoningConfig
                ? $this->reasoning->toArray()
                : $this->reasoning;
        }

        if ($this->text !== null) {
            $data['text'] = $this->text instanceof TextExtendedConfig
                ? $this->text->toArray()
                : $this->text;
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

        if ($this->prompt !== null) {
            $data['prompt'] = $this->prompt instanceof StoredPromptTemplate
                ? $this->prompt->toArray()
                : $this->prompt;
        }

        if ($this->include !== null) {
            $data['include'] = array_map(
                static fn (ResponseIncludes $i): string => $i->value,
                $this->include,
            );
        }

        if ($this->truncation !== null) {
            $data['truncation'] = $this->truncation->value;
        }

        if ($this->models !== null) {
            $data['models'] = $this->models;
        }

        if ($this->modalities !== null) {
            $data['modalities'] = array_map(
                static fn (OutputModality $m): string => $m->value,
                $this->modalities,
            );
        }

        if ($this->serviceTier !== null) {
            $data['service_tier'] = $this->serviceTier->value;
        }

        $optional = [
            'parallel_tool_calls' => $this->parallelToolCalls,
            'temperature' => $this->temperature,
            'top_p' => $this->topP,
            'max_output_tokens' => $this->maxOutputTokens,
            'instructions' => $this->instructions,
            'previous_response_id' => $this->previousResponseId,
            'stream' => $this->stream,
            'metadata' => $this->metadata,
            'image_config' => $this->imageConfig,
            'background' => $this->background,
            'safety_identifier' => $this->safetyIdentifier,
            'session_id' => $this->sessionId,
            'user' => $this->user,
            'frequency_penalty' => $this->frequencyPenalty,
            'presence_penalty' => $this->presencePenalty,
            'top_k' => $this->topK,
            'top_logprobs' => $this->topLogprobs,
            'prompt_cache_key' => $this->promptCacheKey,
            'max_tool_calls' => $this->maxToolCalls,
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
