<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\MessagesContract;
use OpenRouter\Responses\Messages\MessagesResult;
use OpenRouter\Responses\Messages\MessagesStreamEvent;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\ValueObjects\Messages\CreateMessagesRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Messages implements MessagesContract
{
    use Concerns\Streamable;
    use Concerns\Transportable;

    /**
     * Creates a non-streaming message using OpenRouter's Anthropic-compatible
     * `/messages` endpoint. Accepts either a typed
     * {@see CreateMessagesRequest} or a bare array.
     *
     * @see https://openrouter.ai/docs/api-reference/create-messages
     *
     * @param  CreateMessagesRequest|array<string, mixed>  $parameters
     */
    public function send(CreateMessagesRequest|array $parameters): MessagesResult
    {
        $params = $parameters instanceof CreateMessagesRequest
            ? $parameters->toArray()
            : $parameters;

        $this->ensureNotStreamed($params);

        $payload = Payload::create('messages', $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return MessagesResult::from($data, $response->meta());
    }

    /**
     * Creates a streamed message. Returns an iterable that yields one
     * {@see MessagesStreamEvent} (or concrete subclass) per SSE frame.
     *
     * @see https://openrouter.ai/docs/api-reference/create-messages
     *
     * @param  CreateMessagesRequest|array<string, mixed>  $parameters
     * @return StreamResponse<MessagesStreamEvent>
     */
    public function sendStreamed(CreateMessagesRequest|array $parameters): StreamResponse
    {
        $params = $parameters instanceof CreateMessagesRequest
            ? $parameters->toArray()
            : $parameters;

        $params = $this->setStreamParameter($params);

        $payload = Payload::create('messages', $params);

        $response = $this->transporter->requestStream($payload);

        return new StreamResponse(MessagesStreamEvent::class, $response);
    }
}
