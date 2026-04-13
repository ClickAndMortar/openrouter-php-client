<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Concerns;

use OpenRouter\Responses\Meta\MetaInformation;

trait HasMetaInformation
{
    public function meta(): MetaInformation
    {
        return $this->meta;
    }
}
