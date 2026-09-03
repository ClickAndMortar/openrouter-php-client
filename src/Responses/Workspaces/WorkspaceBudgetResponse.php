<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Workspaces;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A single workspace budget.
 *
 * @phpstan-type WorkspaceBudgetResponseType array<string, mixed>
 *
 * @implements ResponseContract<WorkspaceBudgetResponseType>
 */
final class WorkspaceBudgetResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<WorkspaceBudgetResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly WorkspaceBudget $data,
        public readonly ?bool $includeByokInBudgets,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  WorkspaceBudgetResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(WorkspaceBudget::from($raw),
            isset($attributes['include_byok_in_budgets']) ? (bool) $attributes['include_byok_in_budgets'] : null,
            $meta,
        );
    }

    /**
     * @return WorkspaceBudgetResponseType
     */
    public function toArray(): array
    {
        $data = ['data' => $this->data->toArray()];

        if ($this->includeByokInBudgets !== null) {
            $data['include_byok_in_budgets'] = $this->includeByokInBudgets;
        }

        return $data;
    }
}
