<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Generation;

/**
 * Why a generation is being reported. Mirrors the `category` enum accepted by
 * `POST /generation/feedback`.
 */
enum FeedbackCategory: string
{
    case Latency = 'latency';
    case Incoherence = 'incoherence';
    case IncorrectResponse = 'incorrect_response';
    case Formatting = 'formatting';
    case Billing = 'billing';
    case ApiError = 'api_error';
    case Other = 'other';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
