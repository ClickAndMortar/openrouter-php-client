<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses;

/**
 * Allowed values for the `include` request parameter — picks which optional
 * fields the upstream populates in the response.
 */
enum ResponseIncludes: string
{
    case FileSearchCallResults = 'file_search_call.results';
    case MessageInputImageImageUrl = 'message.input_image.image_url';
    case ComputerCallOutputOutputImageUrl = 'computer_call_output.output.image_url';
    case ReasoningEncryptedContent = 'reasoning.encrypted_content';
    case CodeInterpreterCallOutputs = 'code_interpreter_call.outputs';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
