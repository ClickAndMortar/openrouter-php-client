<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Agent\ChatAgent;
use OpenRouter\Contracts\Resources\ChatContract;
use OpenRouter\Responses\Chat\ChatResult;
use OpenRouter\Responses\Chat\Stream\ChatStreamChunk;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\ValueObjects\Chat\CreateChatRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Chat implements ChatContract
{
    use Concerns\Streamable;
    use Concerns\Transportable;

    /**
     * Creates a non-streaming chat completion using OpenRouter's
     * `/chat/completions` endpoint. Accepts either a typed
     * {@see CreateChatRequest} or a bare array.
     *
     * @see https://openrouter.ai/docs/api-reference/chat-completion
     *
     * @param  CreateChatRequest|array<string, mixed>  $parameters
     */
    public function send(CreateChatRequest|array $parameters): ChatResult
    {
        $params = $parameters instanceof CreateChatRequest
            ? $parameters->toArray()
            : $parameters;

        $this->ensureNotStreamed($params);

        $payload = Payload::create('chat/completions', $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ChatResult::from($data, $response->meta());
    }

    /**
     * Creates a streamed chat completion. Returns an iterable that yields one
     * {@see ChatStreamChunk} per SSE frame.
     *
     * @see https://openrouter.ai/docs/api-reference/chat-completion
     *
     * @param  CreateChatRequest|array<string, mixed>  $parameters
     * @return StreamResponse<ChatStreamChunk>
     */
    public function sendStreamed(CreateChatRequest|array $parameters): StreamResponse
    {
        $params = $parameters instanceof CreateChatRequest
            ? $parameters->toArray()
            : $parameters;

        $params = $this->setStreamParameter($params);

        $payload = Payload::create('chat/completions', $params);

        $response = $this->transporter->requestStream($payload);

        return new StreamResponse(ChatStreamChunk::class, $response);
    }

    /**
     * Starts a fluent {@see ChatAgent} for multi-turn tool-use loops against
     * `/chat/completions`. See the README "Agentic helpers" section for usage.
     */
    public function agent(): ChatAgent
    {
        return new ChatAgent($this);
    }
}
