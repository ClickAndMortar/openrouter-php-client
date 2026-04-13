<?php

declare(strict_types=1);

namespace OpenRouter\Contracts;

use OpenRouter\Contracts\Resources\ChatContract;
use OpenRouter\Contracts\Resources\ModelsContract;
use OpenRouter\Contracts\Resources\ResponsesContract;

interface ClientContract
{
    /**
     * Escape hatch for endpoints not yet covered by a typed resource: build a
     * {@see \OpenRouter\ValueObjects\Transporter\Payload} and dispatch it via
     * {@see TransporterContract::requestObject()} or {@see TransporterContract::requestStream()}.
     */
    public function transporter(): TransporterContract;


    /**
     * Create and manage OpenResponses-style generations.
     *
     * @see https://openrouter.ai/docs/api-reference/responses
     */
    public function responses(): ResponsesContract;

    /**
     * Create chat completions using the OpenAI-compatible
     * `/chat/completions` endpoint.
     *
     * @see https://openrouter.ai/docs/api-reference/chat-completion
     */
    public function chat(): ChatContract;

    /**
     * List and inspect available models.
     *
     * @see https://openrouter.ai/docs/api-reference/models
     */
    public function models(): ModelsContract;
}
