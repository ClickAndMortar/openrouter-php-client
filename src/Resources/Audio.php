<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\AudioContract;
use OpenRouter\Responses\Audio\TranscriptionResponse;
use OpenRouter\Responses\BinaryResponse;
use OpenRouter\ValueObjects\Audio\CreateSpeechRequest;
use OpenRouter\ValueObjects\Audio\CreateTranscriptionRequest;
use OpenRouter\ValueObjects\Transporter\Payload;
use OpenRouter\ValueObjects\Transporter\UploadedFile;

final class Audio implements AudioContract
{
    use Concerns\Transportable;

    /**
     * @see https://openrouter.ai/docs/api-reference/audio
     *
     * @param  CreateSpeechRequest|array<string, mixed>  $parameters
     */
    public function speech(CreateSpeechRequest|array $parameters): BinaryResponse
    {
        $params = $parameters instanceof CreateSpeechRequest ? $parameters->toArray() : $parameters;

        return $this->transporter->requestContent(Payload::create('audio/speech', $params));
    }

    /**
     * @param  CreateTranscriptionRequest|array<string, mixed>  $parameters
     */
    public function transcribe(CreateTranscriptionRequest|array $parameters): TranscriptionResponse
    {
        $params = $parameters instanceof CreateTranscriptionRequest ? $parameters->toArray() : $parameters;

        $response = $this->transporter->requestObject(Payload::create('audio/transcriptions', $params));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return TranscriptionResponse::from($data, $response->meta());
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function transcribeFile(UploadedFile $file, string $model, array $options = []): TranscriptionResponse
    {
        $payload = Payload::upload('audio/transcriptions', [
            'file' => $file,
            'model' => $model,
            ...$options,
        ]);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return TranscriptionResponse::from($data, $response->meta());
    }
}
