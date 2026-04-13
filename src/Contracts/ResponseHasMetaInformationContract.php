<?php

declare(strict_types=1);

namespace OpenRouter\Contracts;

use OpenRouter\Responses\Meta\MetaInformation;

interface ResponseHasMetaInformationContract
{
    public function meta(): MetaInformation;
}
