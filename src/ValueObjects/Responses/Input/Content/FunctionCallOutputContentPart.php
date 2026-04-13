<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input\Content;

/**
 * Marker interface for content parts the OpenRouter spec accepts inside a
 * `function_call_output` item. Per `FunctionCallOutputItem.output`, only
 * InputText / InputImage / InputFile are valid — audio and video are not.
 */
interface FunctionCallOutputContentPart extends InputContentPart
{
}
