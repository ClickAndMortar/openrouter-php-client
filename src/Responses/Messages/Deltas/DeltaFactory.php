<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Deltas;

final class DeltaFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): MessagesDelta
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';

        return match ($type) {
            'text_delta' => TextDelta::from($attributes),
            'input_json_delta' => InputJsonDelta::from($attributes),
            'thinking_delta' => ThinkingDelta::from($attributes),
            'signature_delta' => SignatureDelta::from($attributes),
            'citations_delta' => CitationsDelta::from($attributes),
            'compaction_delta' => CompactionDelta::from($attributes),
            default => UnknownDelta::from($attributes),
        };
    }
}
