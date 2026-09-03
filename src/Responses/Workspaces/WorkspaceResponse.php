<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Workspaces;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A single workspace.
 *
 * @phpstan-type WorkspaceResponseType array<string, mixed>
 *
 * @implements ResponseContract<WorkspaceResponseType>
 */
final class WorkspaceResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<WorkspaceResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly Workspace $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  WorkspaceResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(Workspace::from($raw),
            $meta,
        );
    }

    /**
     * @return WorkspaceResponseType
     */
    public function toArray(): array
    {
        $data = ['data' => $this->data->toArray()];

        return $data;
    }
}
