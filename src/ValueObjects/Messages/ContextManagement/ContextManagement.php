<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\ContextManagement;

/**
 * Wraps the `MessagesRequest.context_management` object. Carries an ordered
 * list of typed {@see ContextManagementEdit}s.
 */
final class ContextManagement
{
    /**
     * @param  list<ContextManagementEdit|array<string, mixed>>  $edits
     */
    public function __construct(
        public readonly array $edits,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $rawEdits = isset($attributes['edits']) && is_array($attributes['edits']) ? $attributes['edits'] : [];

        $edits = array_values(array_map(
            static fn (array $e): ContextManagementEdit => ContextManagementEditFactory::from($e),
            array_filter($rawEdits, 'is_array'),
        ));

        return new self($edits);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'edits' => array_map(
                static fn (ContextManagementEdit|array $e): array => $e instanceof ContextManagementEdit
                    ? $e->toArray()
                    : $e,
                $this->edits,
            ),
        ];
    }
}
