<?php

declare(strict_types=1);

namespace OpenRouter;

use OpenRouter\Contracts\ClientContract;
use OpenRouter\Contracts\TransporterContract;
use OpenRouter\Resources\Activity;
use OpenRouter\Resources\Analytics;
use OpenRouter\Resources\Audio;
use OpenRouter\Resources\Auth;
use OpenRouter\Resources\Benchmarks;
use OpenRouter\Resources\Byok;
use OpenRouter\Resources\Chat;
use OpenRouter\Resources\Containers;
use OpenRouter\Resources\Credits;
use OpenRouter\Resources\Datasets;
use OpenRouter\Resources\Embeddings;
use OpenRouter\Resources\Endpoints;
use OpenRouter\Resources\Files;
use OpenRouter\Resources\Generation;
use OpenRouter\Resources\Guardrails;
use OpenRouter\Resources\Images;
use OpenRouter\Resources\Keys;
use OpenRouter\Resources\Messages;
use OpenRouter\Resources\Models;
use OpenRouter\Resources\Observability;
use OpenRouter\Resources\Oauth;
use OpenRouter\Resources\Organization;
use OpenRouter\Resources\Presets;
use OpenRouter\Resources\Providers;
use OpenRouter\Resources\Rerank;
use OpenRouter\Resources\Scim;
use OpenRouter\Resources\Responses;
use OpenRouter\Resources\Videos;
use OpenRouter\Resources\Workspaces;

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

    public function containers(): Containers
    {
        return new Containers($this->transporter);
    }

    public function files(): Files
    {
        return new Files($this->transporter);
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

    public function oauth(): Oauth
    {
        return new Oauth($this->transporter);
    }

    public function audio(): Audio
    {
        return new Audio($this->transporter);
    }

    public function images(): Images
    {
        return new Images($this->transporter);
    }

    public function videos(): Videos
    {
        return new Videos($this->transporter);
    }

    public function analytics(): Analytics
    {
        return new Analytics($this->transporter);
    }

    public function benchmarks(): Benchmarks
    {
        return new Benchmarks($this->transporter);
    }

    public function datasets(): Datasets
    {
        return new Datasets($this->transporter);
    }

    public function byok(): Byok
    {
        return new Byok($this->transporter);
    }

    public function presets(): Presets
    {
        return new Presets($this->transporter);
    }

    public function observability(): Observability
    {
        return new Observability($this->transporter);
    }

    public function scim(): Scim
    {
        return new Scim($this->transporter);
    }

    public function workspaces(): Workspaces
    {
        return new Workspaces($this->transporter);
    }

    public function transporter(): TransporterContract
    {
        return $this->transporter;
    }
}
