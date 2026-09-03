<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Workspaces;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Every budget on a workspace, one per reset interval.
 *
 * `includeByokInBudgets` is a workspace-level setting rather than a property
 * of any single budget, which is why it sits on the response.
 *
 * @phpstan-type ListWorkspaceBudgetsResponseType array<string, mixed>
 *
 * @implements ResponseContract<ListWorkspaceBudgetsResponseType>
 */
final class ListWorkspaceBudgetsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListWorkspaceBudgetsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, WorkspaceBudget>  $data
     */
    private function __construct(
        public readonly array $data,
        public readonly ?int $totalCount,
        public readonly ?bool $includeByokInBudgets,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListWorkspaceBudgetsResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(
            array_values(array_map(
                static fn (array $item): WorkspaceBudget => WorkspaceBudget::from($item),
                array_filter($raw, 'is_array'),
            )),
            is_int($attributes['total_count'] ?? null) ? $attributes['total_count'] : null,
            isset($attributes['include_byok_in_budgets']) ? (bool) $attributes['include_byok_in_budgets'] : null,
            $meta,
        );
    }

    /**
     * @return ListWorkspaceBudgetsResponseType
     */
    public function toArray(): array
    {
        $data = ['data' => array_map(static fn (WorkspaceBudget $i): array => $i->toArray(), $this->data)];

        if ($this->totalCount !== null) {
            $data['total_count'] = $this->totalCount;
        }

        if ($this->includeByokInBudgets !== null) {
            $data['include_byok_in_budgets'] = $this->includeByokInBudgets;
        }

        return $data;
    }
}
