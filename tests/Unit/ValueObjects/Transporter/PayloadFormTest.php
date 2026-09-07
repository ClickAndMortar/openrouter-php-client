<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Transporter;

use OpenRouter\ValueObjects\ApiKey;
use OpenRouter\ValueObjects\Transporter\BaseUri;
use OpenRouter\ValueObjects\Transporter\Headers;
use OpenRouter\ValueObjects\Transporter\Payload;
use OpenRouter\ValueObjects\Transporter\QueryParams;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class PayloadFormTest extends TestCase
{
    private function request(Payload $payload): RequestInterface
    {
        return $payload->toRequest(
            BaseUri::from('openrouter.ai/api/v1'),
            Headers::withAuthorization(ApiKey::from('sk-or-test')),
            QueryParams::create(),
        );
    }

    public function testFormSendsAnUrlEncodedPost(): void
    {
        $request = $this->request(Payload::form('oauth/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:token-exchange',
            'subject_token' => 'header.payload.signature',
        ]));

        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/oauth/token', (string) $request->getUri());
        $this->assertSame('application/x-www-form-urlencoded', $request->getHeaderLine('Content-Type'));
        $this->assertSame(
            'grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Atoken-exchange&subject_token=header.payload.signature',
            (string) $request->getBody(),
        );
    }

    public function testFormOmitsNullFields(): void
    {
        $request = $this->request(Payload::form('oauth/token', [
            'grant_type' => 'token-exchange',
            'scope' => null,
        ]));

        $this->assertSame('grant_type=token-exchange', (string) $request->getBody());
    }

    public function testFormKeepsTheAuthorizationHeaderByDefault(): void
    {
        $request = $this->request(Payload::form('oauth/token', ['a' => 'b']));

        $this->assertSame('Bearer sk-or-test', $request->getHeaderLine('Authorization'));
    }

    public function testFormCanOmitTheAuthorizationHeader(): void
    {
        $request = $this->request(
            Payload::form('oauth/token', ['a' => 'b'], withoutAuthorization: true),
        );

        $this->assertFalse($request->hasHeader('Authorization'));
        // The rest of the request is unaffected.
        $this->assertSame('application/x-www-form-urlencoded', $request->getHeaderLine('Content-Type'));
        $this->assertSame('a=b', (string) $request->getBody());
    }

    public function testCreateWithNoFieldsSendsAnEmptyJsonObjectNotAnArray(): void
    {
        $request = $this->request(Payload::create('scim/sync-jobs', []));

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('{}', (string) $request->getBody());
    }
}
