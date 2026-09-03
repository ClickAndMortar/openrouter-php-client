<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Files;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Confirmation that a file was deleted. The `openrouter` and `anthropic`
 * shapes signal success with `type: file_deleted`; the `openai` shape sends an
 * explicit `deleted` boolean. {@see $deleted} normalises both.
 *
 * @phpstan-type DeleteFileResponseType array<string, mixed>
 *
 * @implements ResponseContract<DeleteFileResponseType>
 */
final class DeleteFileResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<DeleteFileResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly string $id,
        public readonly string $shape,
        public readonly bool $deleted,
        public readonly ?string $type,
        public readonly ?string $object,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  DeleteFileResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : null;

        return new self(
            is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            is_string($attributes['_shape'] ?? null) ? $attributes['_shape'] : 'openrouter',
            isset($attributes['deleted']) ? (bool) $attributes['deleted'] : $type === 'file_deleted',
            $type,
            is_string($attributes['object'] ?? null) ? $attributes['object'] : null,
            $meta,
        );
    }

    /**
     * @return DeleteFileResponseType
     */
    public function toArray(): array
    {
        $data = ['_shape' => $this->shape, 'id' => $this->id];

        if ($this->type !== null) {
            $data['type'] = $this->type;
        }
        if ($this->object !== null) {
            $data['object'] = $this->object;
            $data['deleted'] = $this->deleted;
        }

        return $data;
    }
}
