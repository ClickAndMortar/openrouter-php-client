<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects;

use OpenRouter\ValueObjects\Transporter\BaseUri;
use PHPUnit\Framework\TestCase;

final class BaseUriTest extends TestCase
{
    public function testPrependsHttpsWhenProtocolMissing(): void
    {
        $this->assertSame('https://openrouter.ai/api/v1/', BaseUri::from('openrouter.ai/api/v1')->toString());
    }

    public function testPreservesHttpsWhenProvided(): void
    {
        $this->assertSame('https://openrouter.ai/api/v1/', BaseUri::from('https://openrouter.ai/api/v1')->toString());
    }

    public function testPreservesHttpWhenProvided(): void
    {
        $this->assertSame('http://localhost:8080/', BaseUri::from('http://localhost:8080')->toString());
    }

    public function testStripsTrailingSlashBeforeAppendingIt(): void
    {
        $this->assertSame('https://openrouter.ai/api/v1/', BaseUri::from('https://openrouter.ai/api/v1/')->toString());
    }
}
