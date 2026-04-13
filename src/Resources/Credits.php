<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\CreditsContract;
use OpenRouter\Responses\Credits\RetrieveCreditsResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Credits implements CreditsContract
{
    use Concerns\Transportable;

    /**
     * Returns the total credits purchased and used for the authenticated user.
     * Requires a management key.
     *
     * @see https://openrouter.ai/docs/api-reference/get-credits
     */
    public function retrieve(): RetrieveCreditsResponse
    {
        $payload = Payload::list('credits');

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array{total_credits: float|int, total_usage: float|int}} $data */
        $data = $response->data();

        return RetrieveCreditsResponse::from($data, $response->meta());
    }

    /**
     * Deprecated Coinbase Commerce charge endpoint. The upstream Coinbase APIs have been
     * removed — this call will always fail with HTTP 410 via the transporter's error handler.
     *
     * @deprecated The underlying API endpoint has been permanently removed.
     */
    public function createCoinbaseCharge(): never
    {
        $payload = Payload::create('credits/coinbase', []);

        $this->transporter->requestObject($payload);

        throw new \LogicException('OpenRouter /credits/coinbase is permanently deprecated and should have returned 410 Gone.');
    }
}
