<?php

declare(strict_types=1);

namespace OpenRouter;

use OpenRouter\Contracts\ClientContract;
use OpenRouter\Contracts\TransporterContract;
use OpenRouter\Resources\Activity;
use OpenRouter\Resources\Auth;
use OpenRouter\Resources\Chat;
use OpenRouter\Resources\Credits;
use OpenRouter\Resources\Embeddings;
use OpenRouter\Resources\Endpoints;
use OpenRouter\Resources\Generation;
use OpenRouter\Resources\Guardrails;
use OpenRouter\Resources\Keys;
use OpenRouter\Resources\Messages;
use OpenRouter\Resources\Models;
use OpenRouter\Resources\Organization;
use OpenRouter\Resources\Providers;
use OpenRouter\Resources\Rerank;
use OpenRouter\Resources\Responses;

final class Client implements ClientContract
{
    public function __construct(private readonly TransporterContract $transporter)
    {
    }

    public function responses(): Responses
    {
        return new Responses($this->transporter);
    }

    public function chat(): Chat
    {
        return new Chat($this->transporter);
    }

    public function messages(): Messages
    {
        return new Messages($this->transporter);
    }

    public function models(): Models
    {
        return new Models($this->transporter);
    }

    public function embeddings(): Embeddings
    {
        return new Embeddings($this->transporter);
    }

    public function generation(): Generation
    {
        return new Generation($this->transporter);
    }

    public function activity(): Activity
    {
        return new Activity($this->transporter);
    }

    public function credits(): Credits
    {
        return new Credits($this->transporter);
    }

    public function providers(): Providers
    {
        return new Providers($this->transporter);
    }

    public function endpoints(): Endpoints
    {
        return new Endpoints($this->transporter);
    }

    public function rerank(): Rerank
    {
        return new Rerank($this->transporter);
    }

    public function keys(): Keys
    {
        return new Keys($this->transporter);
    }

    public function guardrails(): Guardrails
    {
        return new Guardrails($this->transporter);
    }

    public function auth(): Auth
    {
        return new Auth($this->transporter);
    }

    public function organization(): Organization
    {
        return new Organization($this->transporter);
    }

    public function transporter(): TransporterContract
    {
        return $this->transporter;
    }
}
