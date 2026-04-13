<?php

declare(strict_types=1);

namespace OpenRouter\Contracts;

use OpenRouter\Exceptions\ErrorException;
use OpenRouter\Exceptions\TransporterException;
use OpenRouter\Exceptions\UnserializableResponse;
use OpenRouter\ValueObjects\Transporter\Payload;
use OpenRouter\ValueObjects\Transporter\Response;
use Psr\Http\Message\ResponseInterface;

interface TransporterContract
{
    public function addHeader(string $name, string $value): self;

    /**
     * Sends a request to a server expecting a JSON object back.
     *
     * @return Response<array<array-key, mixed>>
     *
     * @throws ErrorException|UnserializableResponse|TransporterException
     */
    public function requestObject(Payload $payload): Response;

    /**
     * Sends a stream request to a server.
     *
     * @throws ErrorException|TransporterException
     */
    public function requestStream(Payload $payload): ResponseInterface;
}
