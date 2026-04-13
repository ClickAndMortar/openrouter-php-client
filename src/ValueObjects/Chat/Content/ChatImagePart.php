<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Content;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Image content part for vision models. Mirrors the `ChatContentImage` schema.
 */
final class ChatImagePart implements ChatContentPart
{
    public const DETAILS = ['auto', 'low', 'high'];

    public function __construct(
        public readonly string $url,
        public readonly ?string $detail = null,
    ) {
        if ($this->url === '') {
            throw new InvalidArgumentException('ChatImagePart::$url must not be empty');
        }

        if ($this->detail !== null && ! in_array($this->detail, self::DETAILS, true)) {
            throw new InvalidArgumentException(sprintf(
                'ChatImagePart::$detail must be one of %s, got "%s"',
                implode('/', self::DETAILS),
                $this->detail,
            ));
        }
    }

    public function type(): string
    {
        return 'image_url';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $imageUrl = ['url' => $this->url];
        if ($this->detail !== null) {
            $imageUrl['detail'] = $this->detail;
        }

        return [
            'type' => $this->type(),
            'image_url' => $imageUrl,
        ];
    }
}
