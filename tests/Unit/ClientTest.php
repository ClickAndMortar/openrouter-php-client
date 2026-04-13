<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit;

use OpenRouter\Contracts\TransporterContract;
use OpenRouter\Factory;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testTransporterAccessorReturnsTheSameInstance(): void
    {
        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withHttpClient(new RecordingHttpClient())
            ->make();

        $transporter = $client->transporter();

        $this->assertInstanceOf(TransporterContract::class, $transporter);
        $this->assertSame($transporter, $client->transporter());
    }
}
