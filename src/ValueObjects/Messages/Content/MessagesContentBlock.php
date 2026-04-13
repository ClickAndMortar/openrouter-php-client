<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * Contract for a single content block in the Anthropic `/messages` API.
 *
 * Used on both the request side (entries in `user`/`assistant` message
 * `content` arrays: text, image, document, tool_use, tool_result, thinking,
 * redacted_thinking, server_tool_use, compaction) and the response side
 * (entries in `MessagesResult.content`: adds tool-result variants like
 * web_search_tool_result, web_fetch_tool_result, the code_execution family,
 * tool_search_tool_result, and container_upload).
 *
 * Unknown discriminator values fall back to {@see UnknownContentBlock}.
 */
interface MessagesContentBlock
{
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
