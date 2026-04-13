<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Input\Content\InputContentPart;

/**
 * An input message. Covers both the `EasyInputMessage` and `InputMessageItem`
 * schemas from the OpenRouter OpenAPI spec — the latter adds an optional `id`
 * when replaying a previously returned input item.
 *
 * `content` is either a plain string or a list of {@see InputContentPart}.
 */
final class InputMessage implements InputItem
{
    private const ALLOWED_ROLES = ['user', 'system', 'assistant', 'developer'];

    private const ALLOWED_PHASES = ['commentary', 'final_answer'];

    /**
     * @param  string|list<InputContentPart>  $content
     */
    public function __construct(
        public readonly string $role,
        public readonly string|array $content,
        public readonly ?string $phase = null,
        public readonly ?string $id = null,
    ) {
        if (! in_array($this->role, self::ALLOWED_ROLES, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'InputMessage::$role must be one of %s, got "%s"',
                    implode('/', self::ALLOWED_ROLES),
                    $this->role,
                ),
            );
        }

        if ($this->phase !== null && ! in_array($this->phase, self::ALLOWED_PHASES, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'InputMessage::$phase must be one of %s or null, got "%s"',
                    implode('/', self::ALLOWED_PHASES),
                    $this->phase,
                ),
            );
        }
    }

    /**
     * @param  string|list<InputContentPart>  $content
     */
    public static function user(string|array $content): self
    {
        return new self('user', $content);
    }

    public static function system(string $content): self
    {
        return new self('system', $content);
    }

    /**
     * @param  string|list<InputContentPart>  $content
     */
    public static function assistant(string|array $content, ?string $phase = null): self
    {
        return new self('assistant', $content, $phase);
    }

    public static function developer(string $content): self
    {
        return new self('developer', $content);
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
            'role' => $this->role,
            'content' => is_string($this->content)
                ? $this->content
                : array_map(
                    static fn (InputContentPart $part): array => $part->toArray(),
                    $this->content,
                ),
        ];

        if ($this->phase !== null) {
            $data['phase'] = $this->phase;
        }

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }

        return $data;
    }
}
