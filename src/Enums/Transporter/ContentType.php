<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Transporter;

/**
 * @internal
 */
enum ContentType: string
{
    case JSON = 'application/json';
    case MULTIPART = 'multipart/form-data';
    case FORM = 'application/x-www-form-urlencoded';
    case TEXT_PLAIN = 'text/plain';
}
