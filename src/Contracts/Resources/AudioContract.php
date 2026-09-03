<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Audio\TranscriptionResponse;
use OpenRouter\Responses\BinaryResponse;
use OpenRouter\ValueObjects\Audio\CreateSpeechRequest;
use OpenRouter\ValueObjects\Audio\CreateTranscriptionRequest;
use OpenRouter\ValueObjects\Transporter\UploadedFile;

interface AudioContract
{
    /**
     * Synthesises speech. The response body is raw audio whose Content-Type
     * follows the requested `response_format`.
     *
     * @param  CreateSpeechRequest|array<string, mixed>  $parameters
     */
    public function speech(CreateSpeechRequest|array $parameters): BinaryResponse;

    /**
     * Transcribes audio sent inline as JSON.
     *
     * @param  CreateTranscriptionRequest|array<string, mixed>  $parameters
     */
    public function transcribe(CreateTranscriptionRequest|array $parameters): TranscriptionResponse;

    /**
     * Transcribes an uploaded audio file, sent as `multipart/form-data`.
     *
     * `$options` accepts the remaining form fields: `language`,
     * `response_format`, `temperature` and `timestamp_granularities`.
     *
     * @param  array<string, mixed>  $options
     */
    public function transcribeFile(UploadedFile $file, string $model, array $options = []): TranscriptionResponse;
}
