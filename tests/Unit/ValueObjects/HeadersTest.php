<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects;

use OpenRouter\ValueObjects\ApiKey;
use OpenRouter\ValueObjects\Transporter\Headers;
use PHPUnit\Framework\TestCase;

final class HeadersTest extends TestCase
{
    public function testAuthorizationHeaderIsBearerPrefixed(): void
    {
        $headers = Headers::withAuthorization(ApiKey::from('sk-or-test'));

        $this->assertSame(['Authorization' => 'Bearer sk-or-test'], $headers->toArray());
    }

    public function testWithHttpRefererAddsHttpRefererHeader(): void
    {
        $headers = Headers::create()->withHttpReferer('https://example.com');

        $this->assertSame('https://example.com', $headers->toArray()['HTTP-Referer']);
    }

    public function testWithAppTitleAddsXTitleHeader(): void
    {
        $headers = Headers::create()->withAppTitle('My App');

        $this->assertSame('My App', $headers->toArray()['X-Title']);
    }

    public function testWithAppCategoriesAddsXOpenRouterCategoriesHeader(): void
    {
        $headers = Headers::create()->withAppCategories('cli-agent,cloud-agent');

        $this->assertSame('cli-agent,cloud-agent', $headers->toArray()['X-OpenRouter-Categories']);
    }

    public function testChainingPreservesAllHeaders(): void
    {
        $headers = Headers::withAuthorization(ApiKey::from('sk-or-test'))
            ->withHttpReferer('https://example.com')
            ->withAppTitle('My App')
            ->withAppCategories('cli-agent');

        $this->assertSame([
            'Authorization' => 'Bearer sk-or-test',
            'HTTP-Referer' => 'https://example.com',
            'X-Title' => 'My App',
            'X-OpenRouter-Categories' => 'cli-agent',
        ], $headers->toArray());
    }

    public function testWithSessionIdAddsSessionIdHeader(): void
    {
        $headers = Headers::create()->withSessionId('sess-xyz');

        $this->assertSame('sess-xyz', $headers->toArray()['x-session-id']);
    }

    public function testValueObjectIsImmutable(): void
    {
        $original = Headers::create();
        $modified = $original->withHttpReferer('https://example.com');

        $this->assertSame([], $original->toArray());
        $this->assertSame(['HTTP-Referer' => 'https://example.com'], $modified->toArray());
    }
}
