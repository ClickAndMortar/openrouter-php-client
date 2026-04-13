<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;
use OpenRouter\ValueObjects\Messages\Content\ContentBlockFactory;
use OpenRouter\ValueObjects\Messages\Content\MessagesContentBlock;

/**
 * Typed wrapper around OpenRouter's `MessagesResult` schema for non-streaming
 * `/messages` calls. Mirrors the Anthropic Messages API response format.
 *
 * `content` is a list of typed {@see MessagesContentBlock} instances
 * dispatched from the `type` discriminator (text, tool_use, thinking,
 * server_tool_use, web_search_tool_result, …). Unknown types fall through to
 * {@see \OpenRouter\ValueObjects\Messages\Content\UnknownContentBlock} for
 * forward compatibility.
 *
 * @phpstan-type MessagesResultType array{
 *     id: string,
 *     type: string,
 *     role: string,
 *     model: string,
 *     content: array<int, array<string, mixed>>,
 *     stop_reason?: string|null,
 *     stop_sequence?: string|null,
 *     container?: array<string, mixed>|null,
 *     usage?: array<string, mixed>,
 * }
 *
 * @implements ResponseContract<MessagesResultType>
 */
final class MessagesResult implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<MessagesResultType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  list<MessagesContentBlock>  $content
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $role,
        public readonly string $model,
        public readonly array $content,
        public readonly ?string $stopReason,
        public readonly ?string $stopSequence,
        public readonly ?MessagesContainer $container,
        public readonly ?MessagesUsage $usage,
        public readonly array $extras,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $rawContent = isset($attributes['content']) && is_array($attributes['content'])
            ? $attributes['content']
            : [];
        $content = ContentBlockFactory::fromList($rawContent);

        $known = [
            'id', 'type', 'role', 'model', 'content',
            'stop_reason', 'stop_sequence', 'container', 'usage',
        ];
        $extras = array_diff_key($attributes, array_flip($known));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            type: is_string($attributes['type'] ?? null) ? $attributes['type'] : 'message',
            role: is_string($attributes['role'] ?? null) ? $attributes['role'] : 'assistant',
            model: is_string($attributes['model'] ?? null) ? $attributes['model'] : '',
            content: $content,
            stopReason: is_string($attributes['stop_reason'] ?? null) ? $attributes['stop_reason'] : null,
            stopSequence: is_string($attributes['stop_sequence'] ?? null) ? $attributes['stop_sequence'] : null,
            container: isset($attributes['container']) && is_array($attributes['container'])
                ? MessagesContainer::from($attributes['container'])
                : null,
            usage: isset($attributes['usage']) && is_array($attributes['usage'])
                ? MessagesUsage::from($attributes['usage'])
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
            'type' => $this->type,
            'role' => $this->role,
            'model' => $this->model,
            'content' => array_map(
                static fn (MessagesContentBlock $b): array => $b->toArray(),
                $this->content,
            ),
        ];

        foreach ([
            'stop_reason' => $this->stopReason,
            'stop_sequence' => $this->stopSequence,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        if ($this->container !== null) {
            $data['container'] = $this->container->toArray();
        }

        if ($this->usage !== null) {
            $data['usage'] = $this->usage->toArray();
        }

        return [...$data, ...$this->extras];
    }
}
