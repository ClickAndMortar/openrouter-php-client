<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\Concerns\ItemScopedEvent;

/**
 * `response.image_generation_call.completed` — the image generation tool has
 * finished producing a final image. The image itself is carried on the
 * corresponding output item (see {@see \OpenRouter\Responses\Responses\CreateResponseOutputImageGenerationCall::$result}).
 */
final class CreateStreamedImageGenerationCompletedEvent extends CreateStreamedResponse
{
    use ItemScopedEvent;

    public const EVENT_TYPE = 'response.image_generation_call.completed';
}
