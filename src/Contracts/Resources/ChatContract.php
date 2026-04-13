<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Chat\ChatResult;
use OpenRouter\Responses\Chat\Stream\ChatStreamChunk;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\ValueObjects\Chat\CreateChatRequest;

interface ChatContract
{
    /**
     * Sends a chat completion request to OpenRouter's OpenAI-compatible
     * `/chat/completions` endpoint. Accepts either a typed
     * {@see CreateChatRequest} value object or a bare associative array.
     *
     * @see https://openrouter.ai/docs/api-reference/chat-completion
     *
     * @param  CreateChatRequest|array<string, mixed>  $parameters
     */
    public function send(CreateChatRequest|array $parameters): ChatResult;

    /**
     * Sends a streamed chat completion request. Returns an iterable of
     * {@see ChatStreamChunk}, one per SSE frame.
     *
     * @see https://openrouter.ai/docs/api-reference/chat-completion
     *
     * @param  CreateChatRequest|array<string, mixed>  $parameters
     * @return StreamResponse<ChatStreamChunk>
     */
    public function sendStreamed(CreateChatRequest|array $parameters): StreamResponse;
}
