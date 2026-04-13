<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Citations;

/**
 * Contract for a single citation attached to an Anthropic text block. Concrete
 * implementations correspond to `type` discriminator values: `char_location`,
 * `page_location`, `content_block_location`, `search_result_location`,
 * `web_search_result_location`.
 */
interface MessagesCitation
{
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
