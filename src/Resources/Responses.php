<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Agent\ResponsesAgent;
use OpenRouter\Contracts\Resources\ResponsesContract;
use OpenRouter\Responses\Responses\CreateResponse;
use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\ValueObjects\Responses\CreateResponseRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Responses implements ResponsesContract
{
    use Concerns\Streamable;
    use Concerns\Transportable;

    /**
     * Creates a non-streaming model response using OpenRouter's `/responses` endpoint.
     *
     * Accepts either a typed {@see CreateResponseRequest} or a bare array (BC).
     *
     * @see https://openrouter.ai/docs/api-reference/responses
     *
     * @param  CreateResponseRequest|array<string, mixed>  $parameters
     */
    public function send(CreateResponseRequest|array $parameters): CreateResponse
    {
        $params = $parameters instanceof CreateResponseRequest
            ? $parameters->toArray()
            : $parameters;

        $this->ensureNotStreamed($params);

        $payload = Payload::create('responses', $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        /** @phpstan-ignore-next-line — CreateResponse::from validates the shape at runtime */
        return CreateResponse::from($data, $response->meta());
    }

    /**
     * Creates a streamed model response. Returns an iterable that yields one
     * typed {@see CreateStreamedResponse} subclass per SSE frame.
     *
     * Accepts either a typed {@see CreateResponseRequest} or a bare array (BC).
     *
     * @see https://openrouter.ai/docs/api-reference/responses
     *
     * @param  CreateResponseRequest|array<string, mixed>  $parameters
     * @return StreamResponse<CreateStreamedResponse>
     */
    public function sendStreamed(CreateResponseRequest|array $parameters): StreamResponse
    {
        $params = $parameters instanceof CreateResponseRequest
            ? $parameters->toArray()
            : $parameters;

        $params = $this->setStreamParameter($params);

        $payload = Payload::create('responses', $params);

        $response = $this->transporter->requestStream($payload);

        return new StreamResponse(CreateStreamedResponse::class, $response);
    }

    /**
     * Starts a fluent {@see ResponsesAgent} for multi-turn tool-use loops
     * against `/responses`. See the README "Agentic helpers" section for usage.
     */
    public function agent(): ResponsesAgent
    {
        return new ResponsesAgent($this);
    }
}
