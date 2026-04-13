<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Messages\MessagesResult;
use OpenRouter\Responses\Messages\MessagesStreamEvent;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\ValueObjects\Messages\CreateMessagesRequest;

interface MessagesContract
{
    /**
     * Sends a request to OpenRouter's Anthropic-compatible `/messages`
     * endpoint. Accepts either a typed {@see CreateMessagesRequest} value
     * object or a bare associative array.
     *
     * @see https://openrouter.ai/docs/api-reference/create-messages
     *
     * @param  CreateMessagesRequest|array<string, mixed>  $parameters
     */
    public function send(CreateMessagesRequest|array $parameters): MessagesResult;

    /**
     * Sends a streamed `/messages` request. Returns an iterable of
     * {@see MessagesStreamEvent} (or one of its concrete subclasses), one per
     * SSE frame.
     *
     * @see https://openrouter.ai/docs/api-reference/create-messages
     *
     * @param  CreateMessagesRequest|array<string, mixed>  $parameters
     * @return StreamResponse<MessagesStreamEvent>
     */
    public function sendStreamed(CreateMessagesRequest|array $parameters): StreamResponse;
}
