<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Transporter;

use OpenRouter\ValueObjects\ApiKey;
use OpenRouter\ValueObjects\Transporter\BaseUri;
use OpenRouter\ValueObjects\Transporter\Headers;
use OpenRouter\ValueObjects\Transporter\Payload;
use OpenRouter\ValueObjects\Transporter\QueryParams;
use OpenRouter\ValueObjects\Transporter\UploadedFile;
use PHPUnit\Framework\TestCase;

final class PayloadUploadTest extends TestCase
{
    private function request(Payload $payload): \Psr\Http\Message\RequestInterface
    {
        return $payload->toRequest(
            BaseUri::from('openrouter.ai/api/v1'),
            Headers::withAuthorization(ApiKey::from('sk-or-test')),
            QueryParams::create(),
        );
    }

    public function testUploadSendsAMultipartPostWithABoundary(): void
    {
        $payload = Payload::upload('files', [
            'file' => UploadedFile::fromString('%PDF-1.4 body', 'document.pdf', 'application/pdf'),
        ]);

        $request = $this->request($payload);

        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/files', (string) $request->getUri());
        $this->assertMatchesRegularExpression(
            '#^multipart/form-data; boundary=[A-Za-z0-9]+$#',
            $request->getHeaderLine('Content-Type'),
        );
    }

    public function testUploadEncodesTheFilePart(): void
    {
        $payload = Payload::upload('files', [
            'file' => UploadedFile::fromString('%PDF-1.4 body', 'document.pdf', 'application/pdf'),
        ]);

        $body = (string) $this->request($payload)->getBody();

        $this->assertStringContainsString(
            'Content-Disposition: form-data; name="file"; filename="document.pdf"',
            $body,
        );
        $this->assertStringContainsString('Content-Type: application/pdf', $body);
        $this->assertStringContainsString('%PDF-1.4 body', $body);
    }

    public function testUploadEncodesScalarFieldsAlongsideTheFile(): void
    {
        $payload = Payload::upload('audio/transcriptions', [
            'file' => UploadedFile::fromString('RIFF', 'clip.wav', 'audio/wav'),
            'model' => 'openai/whisper-1',
            'temperature' => 0.2,
        ]);

        $body = (string) $this->request($payload)->getBody();

        $this->assertStringContainsString('Content-Disposition: form-data; name="model"', $body);
        $this->assertStringContainsString('openai/whisper-1', $body);
        $this->assertStringContainsString('Content-Disposition: form-data; name="temperature"', $body);
        $this->assertStringContainsString('0.2', $body);
    }

    public function testUploadRepeatsArrayFieldsAsBracketedParts(): void
    {
        $payload = Payload::upload('audio/transcriptions', [
            'file' => UploadedFile::fromString('RIFF', 'clip.wav', 'audio/wav'),
            'timestamp_granularities' => ['word', 'segment'],
        ]);

        $body = (string) $this->request($payload)->getBody();

        $this->assertSame(
            2,
            substr_count($body, 'name="timestamp_granularities[]"'),
            'each array element should be sent as its own part',
        );
    }

    public function testUploadKeepsQueryParametersInTheUri(): void
    {
        $payload = Payload::upload(
            'files',
            ['file' => UploadedFile::fromString('x', 'a.txt', 'text/plain')],
            ['provider' => 'openai', 'workspace_id' => 'ws_1'],
        );

        $uri = (string) $this->request($payload)->getUri();

        $this->assertStringContainsString('provider=openai', $uri);
        $this->assertStringContainsString('workspace_id=ws_1', $uri);
    }

    public function testUploadedFileFromPathReadsTheFileAndDefaultsTheName(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'or_upload_').'.txt';
        file_put_contents($path, 'hello upload');

        try {
            $file = UploadedFile::fromPath($path);

            $this->assertSame('hello upload', $file->contents);
            $this->assertSame(basename($path), $file->filename);
            $this->assertSame('application/octet-stream', $file->contentType);
        } finally {
            @unlink($path);
        }
    }

    public function testUploadedFileFromPathRejectsAMissingFile(): void
    {
        $this->expectException(\OpenRouter\Exceptions\InvalidArgumentException::class);

        UploadedFile::fromPath('/definitely/not/a/real/path.bin');
    }
}
