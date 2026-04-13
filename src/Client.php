<?php

declare(strict_types=1);

namespace OpenRouter;

use OpenRouter\Contracts\ClientContract;
use OpenRouter\Contracts\TransporterContract;
use OpenRouter\Resources\Chat;
use OpenRouter\Resources\Models;
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

    public function models(): Models
    {
        return new Models($this->transporter);
    }

    public function transporter(): TransporterContract
    {
        return $this->transporter;
    }
}
