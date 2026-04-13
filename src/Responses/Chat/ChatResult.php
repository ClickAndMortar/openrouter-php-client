<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around OpenRouter's `ChatResult` schema for non-streaming
 * `/chat/completions` calls.
 *
 * @phpstan-type ChatResultType array{
 *     id: string,
 *     object: string,
 *     created: int,
 *     model: string,
 *     choices: array<int, array<string, mixed>>,
 *     usage?: array<string, mixed>,
 *     service_tier?: string|null,
 *     system_fingerprint?: string|null,
 *     provider?: string,
 * }
 *
 * @implements ResponseContract<ChatResultType>
 */
final class ChatResult implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ChatResultType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  list<ChatChoice>  $choices
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
        public readonly array $extras,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $rawChoices = isset($attributes['choices']) && is_array($attributes['choices'])
            ? $attributes['choices']
            : [];

        $choices = array_values(array_map(
            static fn (array $c): ChatChoice => ChatChoice::from($c),
            array_filter($rawChoices, 'is_array'),
        ));

        $known = [
            'id', 'object', 'created', 'model', 'choices', 'usage',
            'service_tier', 'system_fingerprint', 'provider',
        ];
        $extras = array_diff_key($attributes, array_flip($known));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            object: is_string($attributes['object'] ?? null) ? $attributes['object'] : 'chat.completion',
            created: is_int($attributes['created'] ?? null) ? $attributes['created'] : 0,
            model: is_string($attributes['model'] ?? null) ? $attributes['model'] : '',
            choices: $choices,
            usage: isset($attributes['usage']) && is_array($attributes['usage'])
                ? ChatUsage::from($attributes['usage'])
                : null,
            serviceTier: isset($attributes['service_tier']) && is_string($attributes['service_tier'])
                ? $attributes['service_tier']
                : null,
            systemFingerprint: isset($attributes['system_fingerprint']) && is_string($attributes['system_fingerprint'])
                ? $attributes['system_fingerprint']
                : null,
            provider: isset($attributes['provider']) && is_string($attributes['provider'])
                ? $attributes['provider']
                : null,
            extras: $extras,
            meta: $meta,
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
            'choices' => array_map(static fn (ChatChoice $c): array => $c->toArray(), $this->choices),
        ];

        if ($this->usage !== null) {
            $data['usage'] = $this->usage->toArray();
        }

        foreach ([
            'service_tier' => $this->serviceTier,
            'system_fingerprint' => $this->systemFingerprint,
            'provider' => $this->provider,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
