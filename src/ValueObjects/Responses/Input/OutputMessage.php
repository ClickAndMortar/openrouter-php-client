<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * An assistant output message replayed as an input item. Mirrors the
 * `OutputMessageItem` shape allowed inside the `Inputs` union (`role=assistant`,
 * `type=message`, content = `output_text` / refusal parts or a string).
 *
 * Content parts pass through as raw arrays — the SDK currently has no typed
 * `ResponseOutputText` / `OpenAIResponsesRefusalContent` VOs, and most callers
 * will forward the array shape received from a prior `/responses` call.
 */
final class OutputMessage implements InputItem
{
    private const ALLOWED_STATUSES = ['in_progress', 'completed', 'incomplete'];

    /**
     * @param  string|list<array<string, mixed>>|null  $content
     */
    public function __construct(
        public readonly string|array|null $content,
        public readonly ?string $id = null,
        public readonly ?string $status = null,
    ) {
        if ($this->status !== null && ! in_array($this->status, self::ALLOWED_STATUSES, true)) {
            throw new InvalidArgumentException(sprintf(
                'OutputMessage::$status must be one of %s or null, got "%s"',
                implode('/', self::ALLOWED_STATUSES),
                $this->status,
            ));
        }
    }

    public function type(): string
    {
        return 'message';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'role' => 'assistant',
            'content' => $this->content,
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }
        if ($this->status !== null) {
            $data['status'] = $this->status;
        }

        return $data;
    }
}
