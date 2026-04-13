<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\Concerns\ItemScopedEvent;

/**
 * `response.image_generation_call.in_progress` — the image generation tool
 * has been invoked and is about to start.
 */
final class CreateStreamedImageGenerationInProgressEvent extends CreateStreamedResponse
{
    use ItemScopedEvent;

    public const EVENT_TYPE = 'response.image_generation_call.in_progress';
}
