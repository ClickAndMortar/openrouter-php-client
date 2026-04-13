<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Responses;

use OpenRouter\Responses\Responses\CostDetails;
use OpenRouter\Responses\Responses\CreateResponseUsage;
use PHPUnit\Framework\TestCase;

final class CreateResponseUsageTest extends TestCase
{
    public function testHydratesCostDetailsAsVo(): void
    {
        $usage = CreateResponseUsage::from([
            'input_tokens' => 100,
            'input_tokens_details' => ['cached_tokens' => 10],
            'output_tokens' => 50,
            'output_tokens_details' => ['reasoning_tokens' => 5],
            'total_tokens' => 150,
            'cost' => 0.0042,
            'cost_details' => [
                'upstream_inference_cost' => 0.003,
                'upstream_inference_input_cost' => 0.001,
                'upstream_inference_output_cost' => 0.002,
            ],
            'is_byok' => true,
        ]);

        $this->assertInstanceOf(CostDetails::class, $usage->costDetails);
        $this->assertSame(0.003, $usage->costDetails->upstreamInferenceCost);
        $this->assertSame(0.001, $usage->costDetails->upstreamInferenceInputCost);
        $this->assertSame(0.002, $usage->costDetails->upstreamInferenceOutputCost);
        $this->assertTrue($usage->isByok);
    }

    public function testToArrayRoundTripsCostDetails(): void
    {
        $input = [
            'input_tokens' => 1,
            'input_tokens_details' => ['cached_tokens' => 0],
            'output_tokens' => 2,
            'output_tokens_details' => ['reasoning_tokens' => 0],
            'total_tokens' => 3,
            'cost_details' => [
                'upstream_inference_input_cost' => 0.01,
                'upstream_inference_output_cost' => 0.02,
            ],
        ];

        $arr = CreateResponseUsage::from($input)->toArray();

        $this->assertArrayHasKey('cost_details', $arr);
        $this->assertSame(0.01, $arr['cost_details']['upstream_inference_input_cost']);
        $this->assertSame(0.02, $arr['cost_details']['upstream_inference_output_cost']);
        $this->assertArrayNotHasKey('upstream_inference_cost', $arr['cost_details']);
    }

    public function testCostDetailsForwardCompatExtras(): void
    {
        $details = CostDetails::from([
            'upstream_inference_input_cost' => 0.5,
            'upstream_inference_output_cost' => 1.0,
            'unknown_future_field' => 'preserved',
        ]);

        $this->assertSame('preserved', $details->extras['unknown_future_field']);
        $this->assertSame('preserved', $details->toArray()['unknown_future_field']);
    }

    public function testMissingCostDetailsRemainsNull(): void
    {
        $usage = CreateResponseUsage::from([
            'input_tokens' => 1,
            'input_tokens_details' => ['cached_tokens' => 0],
            'output_tokens' => 2,
            'output_tokens_details' => ['reasoning_tokens' => 0],
            'total_tokens' => 3,
        ]);

        $this->assertNull($usage->costDetails);
        $this->assertArrayNotHasKey('cost_details', $usage->toArray());
    }
}
