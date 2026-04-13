<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input\Content;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Image content part inside an input message. Mirrors the `InputImage` schema
 * from the OpenRouter OpenAPI spec.
 *
 * `imageUrl` can be a remote URL or a base64 data URL (`data:image/png;base64,...`).
 * `detail` controls how the model processes the image resolution.
 */
final class InputImagePart implements FunctionCallOutputContentPart
{
    public function __construct(
        public readonly ?string $imageUrl,
        public readonly string $detail = 'auto',
    ) {
        if (! in_array($this->detail, ['auto', 'high', 'low'], true)) {
            throw new InvalidArgumentException(
                sprintf('InputImagePart::$detail must be one of auto/high/low, got "%s"', $this->detail),
            );
        }
    }

    public function type(): string
    {
        return 'input_image';
    }

    /**
     * @return array{type: string, image_url: string|null, detail: string}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'image_url' => $this->imageUrl,
            'detail' => $this->detail,
        ];
    }
}
