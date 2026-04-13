<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Deltas;

/**
 * Contract for a single `delta` payload on a streamed
 * `content_block_delta` event. Concrete implementations correspond to the
 * `type` discriminator: text_delta, input_json_delta, thinking_delta,
 * signature_delta, citations_delta, compaction_delta. Unknown types fall
 * back to {@see UnknownDelta}.
 */
interface MessagesDelta
{
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
