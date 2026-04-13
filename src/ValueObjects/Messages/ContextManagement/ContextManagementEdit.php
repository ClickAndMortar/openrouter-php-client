<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\ContextManagement;

/**
 * Contract for a single entry in `MessagesRequest.context_management.edits[]`.
 * Concrete implementations correspond to the `type` discriminator
 * (`clear_tool_uses_20250919`, `clear_thinking_20251015`, `compact_20260112`).
 * Unknown types fall back to {@see UnknownContextManagementEdit}.
 */
interface ContextManagementEdit
{
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
