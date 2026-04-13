<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

use OpenRouter\Enums\Responses\Tools\ImageQuality;
use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Image-generation server tool (gpt-image-1 family). All fields optional;
 * upstream defaults apply when omitted. Other enum-shaped fields stay as
 * plain strings for forward compatibility with new provider-specific values.
 */
final class ImageGenerationServerTool implements Tool
{
    /**
     * @param  array<string, mixed>|null  $inputImageMask
     */
    public function __construct(
        public readonly ?string $model = null,
        public readonly ?ImageQuality $quality = null,
        public readonly ?string $size = null,
        public readonly ?string $outputFormat = null,
        public readonly ?int $outputCompression = null,
        public readonly ?int $partialImages = null,
        public readonly ?string $inputFidelity = null,
        public readonly ?array $inputImageMask = null,
        public readonly ?string $background = null,
        public readonly ?string $moderation = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $quality = null;
        if (isset($attributes['quality']) && is_string($attributes['quality'])) {
            $quality = ImageQuality::tryFrom($attributes['quality']);
            if ($quality === null) {
                throw new InvalidArgumentException(sprintf(
                    'ImageGenerationServerTool::$quality must be one of %s, got "%s"',
                    implode('/', ImageQuality::values()),
                    $attributes['quality'],
                ));
            }
        }

        return new self(
            model: isset($attributes['model']) && is_string($attributes['model']) ? $attributes['model'] : null,
            quality: $quality,
            size: isset($attributes['size']) && is_string($attributes['size']) ? $attributes['size'] : null,
            outputFormat: isset($attributes['output_format']) && is_string($attributes['output_format']) ? $attributes['output_format'] : null,
            outputCompression: isset($attributes['output_compression']) ? (int) $attributes['output_compression'] : null,
            partialImages: isset($attributes['partial_images']) ? (int) $attributes['partial_images'] : null,
            inputFidelity: isset($attributes['input_fidelity']) && is_string($attributes['input_fidelity']) ? $attributes['input_fidelity'] : null,
            inputImageMask: isset($attributes['input_image_mask']) && is_array($attributes['input_image_mask']) ? $attributes['input_image_mask'] : null,
            background: isset($attributes['background']) && is_string($attributes['background']) ? $attributes['background'] : null,
            moderation: isset($attributes['moderation']) && is_string($attributes['moderation']) ? $attributes['moderation'] : null,
        );
    }

    public function type(): string
    {
        return 'image_generation';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type()];

        $optional = [
            'model' => $this->model,
            'quality' => $this->quality?->value,
            'size' => $this->size,
            'output_format' => $this->outputFormat,
            'output_compression' => $this->outputCompression,
            'partial_images' => $this->partialImages,
            'input_fidelity' => $this->inputFidelity,
            'input_image_mask' => $this->inputImageMask,
            'background' => $this->background,
            'moderation' => $this->moderation,
        ];

        foreach ($optional as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
