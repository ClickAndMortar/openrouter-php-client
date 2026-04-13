<?php

declare(strict_types=1);

namespace OpenRouter\Resources\Concerns;

use OpenRouter\Contracts\TransporterContract;

trait Transportable
{
    public function __construct(private readonly TransporterContract $transporter)
    {
    }
}
