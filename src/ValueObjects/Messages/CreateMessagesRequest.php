<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages;

use OpenRouter\Enums\Messages\MessagesServiceTier;
use OpenRouter\Enums\Messages\MessagesSpeed;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Messages\Config\MessagesOutputConfig;
use OpenRouter\ValueObjects\Messages\Config\MessagesThinkingConfig;
use OpenRouter\ValueObjects\Messages\Config\MessagesToolChoice;
use OpenRouter\ValueObjects\Messages\ContextManagement\ContextManagement;
use OpenRouter\ValueObjects\Messages\Content\MessagesCacheControl;
use OpenRouter\ValueObjects\Messages\Content\MessagesContentBlock;
use OpenRouter\ValueObjects\Messages\Messages\MessagesMessage;
use OpenRouter\ValueObjects\Messages\Tools\MessagesTool;
use OpenRouter\ValueObjects\Responses\Plugins\Plugin;

/**
 * Typed builder for a `POST /messages` request. Mirrors the Anthropic-style
 * `MessagesRequest` schema. Validates that `messages` is non-empty at
 * construction time and serializes via {@see toArray()}.
 *
 * The `/messages` request body contains many deeply-nested discriminated
 * unions (tools, plugins, thinking modes, content blocks, context_management
 * edits, etc.). V1 keeps those nested fields as raw arrays — consumers pass
 * them through unchanged. Only the top-level shape is typed. Unknown fields
 * pass through `$extras` unchanged.
 */
final class CreateMessagesRequest
{
    private const IDENTIFIER_MAX_LENGTH = 256;

    /**
     * @param  list<MessagesMessage|array<string, mixed>>  $messages
     * @param  string|list<MessagesContentBlock|array<string, mixed>>|null  $system
     * @param  list<MessagesTool|array<string, mixed>>|null  $tools
     * @param  MessagesToolChoice|array<string, mixed>|null  $toolChoice
     * @param  MessagesThinkingConfig|array<string, mixed>|null  $thinking
     * @param  ContextManagement|array<string, mixed>|null  $contextManagement
     * @param  MessagesCacheControl|array<string, mixed>|null  $cacheControl
     * @param  MessagesOutputConfig|array<string, mixed>|null  $outputConfig
     * @param  list<Plugin|array<string, mixed>>|null  $plugins
     * @param  array<string, mixed>|null  $provider
     * @param  array<string, mixed>|null  $trace
     * @param  array<string, mixed>|null  $metadata
     * @param  list<string>|null  $stopSequences
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly array $messages,
        public readonly ?string $model = null,
        public readonly ?int $maxTokens = null,
        public readonly ?float $temperature = null,
        public readonly ?float $topP = null,
        public readonly ?int $topK = null,
        public readonly ?array $stopSequences = null,
        public readonly ?bool $stream = null,
        public readonly string|array|null $system = null,
        public readonly ?string $user = null,
        public readonly ?string $sessionId = null,
        public readonly ?array $metadata = null,
        public readonly ?array $tools = null,
        public readonly mixed $toolChoice = null,
        public readonly mixed $thinking = null,
        public readonly mixed $contextManagement = null,
        public readonly mixed $cacheControl = null,
        public readonly mixed $outputConfig = null,
        public readonly ?array $plugins = null,
        public readonly ?array $provider = null,
        public readonly ?MessagesServiceTier $serviceTier = null,
        public readonly ?MessagesSpeed $speed = null,
        public readonly ?array $trace = null,
        public readonly ?string $route = null,
        public readonly array $extras = [],
    ) {
        if ($this->messages === []) {
            throw new InvalidArgumentException('CreateMessagesRequest::$messages must not be empty');
        }

        if ($this->model !== null && $this->model === '') {
            throw new InvalidArgumentException('CreateMessagesRequest::$model must not be an empty string');
        }

        if ($this->maxTokens !== null && $this->maxTokens < 1) {
            throw new InvalidArgumentException('CreateMessagesRequest::$maxTokens must be >= 1');
        }

        $this->validateIdentifier('user', $this->user);
        $this->validateIdentifier('sessionId', $this->sessionId);
    }

    private function validateIdentifier(string $field, ?string $value): void
    {
        if ($value === null) {
            return;
        }

        if (mb_strlen($value) > self::IDENTIFIER_MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf(
                'CreateMessagesRequest::$%s must be <= %d characters',
                $field,
                self::IDENTIFIER_MAX_LENGTH,
            ));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'messages' => array_map(
                static fn (MessagesMessage|array $m): array => $m instanceof MessagesMessage ? $m->toArray() : $m,
                $this->messages,
            ),
        ];

        if ($this->system !== null) {
            $data['system'] = is_string($this->system)
                ? $this->system
                : array_map(
                    static fn (MessagesContentBlock|array $b): array => $b instanceof MessagesContentBlock
                        ? $b->toArray()
                        : $b,
                    $this->system,
                );
        }

        if ($this->tools !== null) {
            $data['tools'] = array_map(
                static fn (MessagesTool|array $t): array => $t instanceof MessagesTool ? $t->toArray() : $t,
                $this->tools,
            );
        }

        if ($this->plugins !== null) {
            $data['plugins'] = array_map(
                static fn (Plugin|array $p): array => $p instanceof Plugin ? $p->toArray() : $p,
                $this->plugins,
            );
        }

        if ($this->toolChoice !== null) {
            $data['tool_choice'] = $this->toolChoice instanceof MessagesToolChoice
                ? $this->toolChoice->toArray()
                : $this->toolChoice;
        }

        if ($this->thinking !== null) {
            $data['thinking'] = $this->thinking instanceof MessagesThinkingConfig
                ? $this->thinking->toArray()
                : $this->thinking;
        }

        if ($this->contextManagement !== null) {
            $data['context_management'] = $this->contextManagement instanceof ContextManagement
                ? $this->contextManagement->toArray()
                : $this->contextManagement;
        }

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl instanceof MessagesCacheControl
                ? $this->cacheControl->toArray()
                : $this->cacheControl;
        }

        if ($this->outputConfig !== null) {
            $data['output_config'] = $this->outputConfig instanceof MessagesOutputConfig
                ? $this->outputConfig->toArray()
                : $this->outputConfig;
        }

        $optional = [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
            'top_p' => $this->topP,
            'top_k' => $this->topK,
            'stop_sequences' => $this->stopSequences,
            'stream' => $this->stream,
            'user' => $this->user,
            'session_id' => $this->sessionId,
            'metadata' => $this->metadata,
            'provider' => $this->provider,
            'trace' => $this->trace,
            'route' => $this->route,
        ];

        foreach ($optional as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        if ($this->serviceTier !== null) {
            $data['service_tier'] = $this->serviceTier->value;
        }

        if ($this->speed !== null) {
            $data['speed'] = $this->speed->value;
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
