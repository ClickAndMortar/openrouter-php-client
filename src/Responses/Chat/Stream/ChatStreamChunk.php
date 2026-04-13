<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat\Stream;

use OpenRouter\Responses\Chat\ChatUsage;

/**
 * A single SSE frame from a streaming chat completion. Mirrors
 * `ChatStreamChunk`. Hydrated by {@see \OpenRouter\Responses\StreamResponse}
 * via the `from()` factory — note the single-arg signature (no
 * `MetaInformation` second arg, unlike the non-streaming `ChatResult`), since
 * `StreamResponse` calls `$class::from($payload)` without a meta arg.
 *
 * The `usage` field is typically only present on the final frame before
 * `[DONE]`.
 */
final class ChatStreamChunk
{
    /**
     * @param  list<ChatStreamChoice>  $choices
     * @param  array<string, mixed>|null  $error
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $object,
        public readonly int $created,
        public readonly string $model,
        public readonly array $choices,
        public readonly ?ChatUsage $usage,
        public readonly ?string $serviceTier,
        public readonly ?string $systemFingerprint,
        public readonly ?string $provider,
        public readonly ?array $error,
        public readonly array $extras,
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function from(array $payload): self
    {
        $rawChoices = isset($payload['choices']) && is_array($payload['choices']) ? $payload['choices'] : [];

        $choices = array_values(array_map(
            static fn (array $c): ChatStreamChoice => ChatStreamChoice::from($c),
            array_filter($rawChoices, 'is_array'),
        ));

        $known = [
            'id', 'object', 'created', 'model', 'choices', 'usage',
            'service_tier', 'system_fingerprint', 'provider', 'error',
        ];
        $extras = array_diff_key($payload, array_flip($known));

        return new self(
            id: is_string($payload['id'] ?? null) ? $payload['id'] : '',
            object: is_string($payload['object'] ?? null) ? $payload['object'] : 'chat.completion.chunk',
            created: is_int($payload['created'] ?? null) ? $payload['created'] : 0,
            model: is_string($payload['model'] ?? null) ? $payload['model'] : '',
            choices: $choices,
            usage: isset($payload['usage']) && is_array($payload['usage'])
                ? ChatUsage::from($payload['usage'])
                : null,
            serviceTier: isset($payload['service_tier']) && is_string($payload['service_tier'])
                ? $payload['service_tier']
                : null,
            systemFingerprint: isset($payload['system_fingerprint']) && is_string($payload['system_fingerprint'])
                ? $payload['system_fingerprint']
                : null,
            provider: isset($payload['provider']) && is_string($payload['provider']) ? $payload['provider'] : null,
            error: isset($payload['error']) && is_array($payload['error']) ? $payload['error'] : null,
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'object' => $this->object,
            'created' => $this->created,
            'model' => $this->model,
            'choices' => array_map(static fn (ChatStreamChoice $c): array => $c->toArray(), $this->choices),
        ];

        if ($this->usage !== null) {
            $data['usage'] = $this->usage->toArray();
        }

        foreach ([
            'service_tier' => $this->serviceTier,
            'system_fingerprint' => $this->systemFingerprint,
            'provider' => $this->provider,
            'error' => $this->error,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
