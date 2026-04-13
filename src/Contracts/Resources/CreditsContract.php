<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Credits\RetrieveCreditsResponse;

interface CreditsContract
{
    /**
     * Returns the total credits purchased and used for the authenticated user.
     * Requires a management key.
     *
     * @see https://openrouter.ai/docs/api-reference/get-credits
     */
    public function retrieve(): RetrieveCreditsResponse;

    /**
     * Deprecated Coinbase Commerce charge endpoint. The upstream Coinbase APIs have been
     * removed — this call will always fail with HTTP 410. Use the web credits purchase flow instead.
     *
     * @deprecated The underlying API endpoint has been permanently removed.
     */
    public function createCoinbaseCharge(): never;
}
