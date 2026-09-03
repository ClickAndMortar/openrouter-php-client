<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Audio\TranscriptionResponse;
use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\AudioTranscriptionFixture;
use OpenRouter\ValueObjects\Audio\CreateSpeechRequest;
use OpenRouter\ValueObjects\Audio\CreateTranscriptionRequest;
use OpenRouter\ValueObjects\Transporter\UploadedFile;
use PHPUnit\Framework\TestCase;

final class AudioTest extends TestCase
{
    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testSpeechPostsTheRequestAndReturnsRawAudio(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueBinary("ID3\x03mp3-bytes", 'audio/mpeg');

        $response = $this->client($http)->audio()->speech(new CreateSpeechRequest(
            model: 'openai/tts-1',
            input: 'Hello there',
            voice: 'alloy',
            responseFormat: 'mp3',
            speed: 1.25,
        ));

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/audio/speech', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('openai/tts-1', $body['model']);
        $this->assertSame('Hello there', $body['input']);
        $this->assertSame('alloy', $body['voice']);
        $this->assertSame('mp3', $body['response_format']);
        $this->assertSame(1.25, $body['speed']);

        $this->assertInstanceOf(BinaryResponse::class, $response);
        $this->assertSame("ID3\x03mp3-bytes", $response->contents);
        $this->assertSame('audio/mpeg', $response->contentType);
    }

    public function testSpeechRejectsEmptyInput(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CreateSpeechRequest(model: 'openai/tts-1', input: '');
    }

    public function testTranscribeSendsJsonWhenGivenInlineAudio(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(AudioTranscriptionFixture::ATTRIBUTES);

        $this->client($http)->audio()->transcribe(new CreateTranscriptionRequest(
            model: 'openai/whisper-1',
            inputAudio: ['data' => 'UklGRg==', 'format' => 'wav'],
            language: 'en',
            temperature: 0.0,
            timestampGranularities: ['word', 'segment'],
        ));

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/audio/transcriptions', (string) $request->getUri());
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('openai/whisper-1', $body['model']);
        $this->assertSame(['data' => 'UklGRg==', 'format' => 'wav'], $body['input_audio']);
        $this->assertSame('en', $body['language']);
        $this->assertSame(['word', 'segment'], $body['timestamp_granularities']);
    }

    public function testTranscribeReturnsATypedTranscription(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(AudioTranscriptionFixture::ATTRIBUTES);

        $response = $this->client($http)->audio()->transcribe([
            'model' => 'openai/whisper-1',
            'input_audio' => ['data' => 'UklGRg=='],
        ]);

        $this->assertInstanceOf(TranscriptionResponse::class, $response);
        $this->assertSame('Hello from OpenRouter.', $response->text);
        $this->assertSame('en', $response->language);
        $this->assertSame(2.5, $response->duration);

        $this->assertCount(1, $response->segments);
        $this->assertSame(0, $response->segments[0]->id);
        $this->assertSame(2.5, $response->segments[0]->end);
        $this->assertSame('Hello from OpenRouter.', $response->segments[0]->text);

        $this->assertCount(1, $response->words);
        $this->assertSame('Hello', $response->words[0]->word);
        $this->assertSame(0.4, $response->words[0]->end);

        $this->assertSame(36, $response->usage?->totalTokens);
        $this->assertSame(2.5, $response->usage?->seconds);
    }

    public function testTranscribeFileSendsMultipart(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(AudioTranscriptionFixture::ATTRIBUTES);

        $response = $this->client($http)->audio()->transcribeFile(
            UploadedFile::fromString('RIFFwave', 'clip.wav', 'audio/wav'),
            'openai/whisper-1',
            ['language' => 'en', 'timestamp_granularities' => ['word', 'segment']],
        );

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/audio/transcriptions', (string) $request->getUri());
        $this->assertStringStartsWith('multipart/form-data;', $request->getHeaderLine('Content-Type'));

        $body = (string) $request->getBody();
        $this->assertStringContainsString('filename="clip.wav"', $body);
        $this->assertStringContainsString('name="model"', $body);
        $this->assertStringContainsString('openai/whisper-1', $body);
        $this->assertStringContainsString('name="language"', $body);
        $this->assertSame(2, substr_count($body, 'name="timestamp_granularities[]"'));

        $this->assertInstanceOf(TranscriptionResponse::class, $response);
        $this->assertSame('Hello from OpenRouter.', $response->text);
    }

    public function testTranscriptionKeepsUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([...AudioTranscriptionFixture::ATTRIBUTES, 'a_new_field' => true]);

        $response = $this->client($http)->audio()->transcribe(['model' => 'm', 'input_audio' => []]);

        $this->assertTrue($response->extras['a_new_field']);
    }
}
