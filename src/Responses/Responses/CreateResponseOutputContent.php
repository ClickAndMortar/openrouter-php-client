<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * Typed representation of a single `content` item inside an OpenRouter
 * response output message. Models both `output_text` (with typed
 * annotations) and `refusal` shapes; anything else is preserved as raw
 * extras.
 *
 * @phpstan-type CreateResponseOutputContentType array{
 *     type: string,
 *     text?: string,
 *     refusal?: string,
 *     refusal_reason?: string,
 *     annotations?: array<int, array<string, mixed>>,
 *     logprobs?: array<int, array<string, mixed>>,
 * }
 */
final class CreateResponseOutputContent
{
    /**
     * @param  array<int, CreateResponseOutputAnnotation>  $annotations
     * @param  list<LogProbs>  $logprobs
     * @param  array<string, mixed>  $extra
     */
    private function __construct(
        public readonly string $type,
        public readonly ?string $text,
        public readonly ?string $refusal,
        public readonly ?string $refusalReason,
        public readonly array $annotations,
        public readonly array $logprobs,
        public readonly array $extra,
    ) {
    }

    /**
     * @param  CreateResponseOutputContentType  $attributes
     */
    public static function from(array $attributes): self
    {
        $known = ['type', 'text', 'refusal', 'refusal_reason', 'annotations', 'logprobs'];
        $extra = array_diff_key($attributes, array_flip($known));

        $rawAnnotations = isset($attributes['annotations']) && is_array($attributes['annotations'])
            ? $attributes['annotations']
            : [];

        $annotations = array_map(
            static fn (array $a): CreateResponseOutputAnnotation => CreateResponseOutputAnnotation::from($a),
            $rawAnnotations,
        );

        $logprobs = [];
        if (isset($attributes['logprobs']) && is_array($attributes['logprobs'])) {
            foreach ($attributes['logprobs'] as $entry) {
                if (is_array($entry)) {
                    $logprobs[] = LogProbs::from($entry);
                }
            }
        }

        return new self(
            type: $attributes['type'],
            text: $attributes['text'] ?? null,
            refusal: isset($attributes['refusal']) && is_string($attributes['refusal']) ? $attributes['refusal'] : null,
            refusalReason: isset($attributes['refusal_reason']) && is_string($attributes['refusal_reason']) ? $attributes['refusal_reason'] : null,
            annotations: $annotations,
            logprobs: $logprobs,
            extra: $extra,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type];

        if ($this->text !== null) {
            $data['text'] = $this->text;
        }
        if ($this->refusal !== null) {
            $data['refusal'] = $this->refusal;
        }
        if ($this->refusalReason !== null) {
            $data['refusal_reason'] = $this->refusalReason;
        }
        if ($this->annotations !== []) {
            $data['annotations'] = array_map(
                static fn (CreateResponseOutputAnnotation $a): array => $a->toArray(),
                $this->annotations,
            );
        }
        if ($this->logprobs !== []) {
            $data['logprobs'] = array_map(
                static fn (LogProbs $l): array => $l->toArray(),
                $this->logprobs,
            );
        }

        foreach ($this->extra as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
