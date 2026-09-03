<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Transporters;

use OpenRouter\Exceptions\Http\NotFoundException;
use OpenRouter\Factory;
use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\ValueObjects\Transporter\Payload;
use PHPUnit\Framework\TestCase;

final class BinaryResponseTest extends TestCase
{
    private function transporter(RecordingHttpClient $http): \OpenRouter\Contracts\TransporterContract
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make()->transporter();
    }

    public function testRequestContentReturnsRawBytesUndecoded(): void
    {
        $http = new RecordingHttpClient();
        // Deliberately not valid UTF-8 or JSON — this is what an mp3 body looks like.
        $bytes = "ID3\x03\x00\x00\x00\xFF\xFB\x90";
        $http->enqueueBinary($bytes, 'audio/mpeg');

        $response = $this->transporter($http)->requestContent(Payload::create('audio/speech', ['model' => 'x']));

        $this->assertInstanceOf(BinaryResponse::class, $response);
        $this->assertSame($bytes, $response->contents);
        $this->assertSame('audio/mpeg', $response->contentType);
        $this->assertSame(strlen($bytes), $response->sizeInBytes());
    }

    public function testRequestContentExposesResponseMeta(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueBinary('abc', 'application/octet-stream', 200, ['x-request-id' => 'req_42']);

        $response = $this->transporter($http)->requestContent(Payload::list('files/f_1/content'));

        $this->assertSame('req_42', $response->meta()->requestId);
    }

    public function testSaveToWritesTheBytesToDisk(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueBinary('video-bytes', 'video/mp4');

        $response = $this->transporter($http)->requestContent(Payload::list('videos/job_1/content'));

        $path = sys_get_temp_dir().'/or_binary_'.bin2hex(random_bytes(4)).'.mp4';

        try {
            $written = $response->saveTo($path);

            $this->assertSame(11, $written);
            $this->assertSame('video-bytes', file_get_contents($path));
        } finally {
            @unlink($path);
        }
    }

    public function testRequestContentStillRaisesApiErrors(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'No such file']], 404);

        $this->expectException(NotFoundException::class);

        $this->transporter($http)->requestContent(Payload::list('files/nope/content'));
    }
}
