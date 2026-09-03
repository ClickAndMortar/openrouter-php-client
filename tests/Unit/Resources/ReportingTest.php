<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Enums\Generation\FeedbackCategory;
use OpenRouter\Factory;
use OpenRouter\Responses\Benchmarks\ListBenchmarksResponse;
use OpenRouter\Responses\DataResponse;
use OpenRouter\Responses\Datasets\AppRankingsResponse;
use OpenRouter\Responses\Datasets\DailyRankingsResponse;
use OpenRouter\Responses\Datasets\SessionCostResponse;
use OpenRouter\Responses\Models\RetrieveModelResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ModelsListFixture;
use PHPUnit\Framework\TestCase;

final class ReportingTest extends TestCase
{
    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testModelsRetrieveReturnsASingleTypedModel(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => ModelsListFixture::ATTRIBUTES['data'][0]]);

        $response = $this->client($http)->models()->retrieve('openai', 'gpt-4');

        $this->assertSame('GET', $http->lastRequest()->getMethod());
        $this->assertStringEndsWith('/model/openai/gpt-4', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(RetrieveModelResponse::class, $response);
        $this->assertSame('openai/gpt-4', $response->data->id);
        $this->assertSame(8192, $response->data->contextLength);
    }

    public function testGenerationContentRequiresTheGenerationId(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => ['input' => 'hi', 'output' => ['text' => 'hello'], 'error' => null]]);

        $response = $this->client($http)->generation()->content('gen_1');

        $uri = (string) $http->lastRequest()->getUri();
        $this->assertStringContainsString('/generation/content', $uri);
        $this->assertStringContainsString('id=gen_1', $uri);
        $this->assertInstanceOf(DataResponse::class, $response);
        $this->assertSame('hi', $response->data['input']);
        $this->assertSame(['text' => 'hello'], $response->data['output']);
    }

    public function testGenerationFeedbackPostsCategoryAndComment(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => ['id' => 'fb_1']]);

        $this->client($http)->generation()->submitFeedback(
            'gen_1',
            FeedbackCategory::IncorrectResponse,
            'The answer cited a page that does not exist.',
        );

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/generation/feedback', (string) $request->getUri());
        $this->assertSame(
            [
                'generation_id' => 'gen_1',
                'category' => 'incorrect_response',
                'comment' => 'The answer cited a page that does not exist.',
            ],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testFeedbackCategoryCoversTheDocumentedValues(): void
    {
        $this->assertSame(
            ['latency', 'incoherence', 'incorrect_response', 'formatting', 'billing', 'api_error', 'other'],
            FeedbackCategory::values(),
        );
    }

    public function testAnalyticsMetaAndQuery(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => ['dimensions' => [['name' => 'model', 'display_label' => 'Model']]]]);
        $http->enqueueJson(['data' => ['rows' => []]]);

        $analytics = $this->client($http)->analytics();

        $meta = $analytics->meta();
        $this->assertStringEndsWith('/analytics/meta', (string) $http->lastRequest()->getUri());
        $this->assertSame('model', $meta->data['dimensions'][0]['name']);

        $analytics->query(['metrics' => ['tokens'], 'granularity' => 'day']);
        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/analytics/query', (string) $request->getUri());
        $this->assertSame(
            ['metrics' => ['tokens'], 'granularity' => 'day'],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testDatasetsAppRankings(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([
            'data' => [[
                'rank' => 1,
                'app_id' => 'app_1',
                'app_name' => 'Cursor',
                'total_tokens' => 1234567,
                'total_requests' => 4242,
            ]],
            'meta' => ['generated_at' => '2026-09-01'],
        ]);

        $response = $this->client($http)->datasets()->appRankings(category: 'programming', limit: 10);

        $query = [];
        parse_str($http->lastRequest()->getUri()->getQuery(), $query);
        $this->assertStringContainsString('/datasets/app-rankings', (string) $http->lastRequest()->getUri());
        $this->assertSame('programming', $query['category']);
        $this->assertSame('10', $query['limit']);

        $this->assertInstanceOf(AppRankingsResponse::class, $response);
        $this->assertSame(1, $response->data[0]->rank);
        $this->assertSame('Cursor', $response->data[0]->appName);
        $this->assertSame(1234567, $response->data[0]->totalTokens);
        $this->assertSame(['generated_at' => '2026-09-01'], $response->metadata);
    }

    public function testDatasetsDailyRankingsAndSessionCost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([
            'data' => [['date' => '2026-09-01', 'model_permaslug' => 'openai/gpt-4o', 'total_tokens' => 999]],
            'meta' => [],
        ]);
        $http->enqueueJson([
            'data' => [[
                'app_slug' => 'cursor',
                'app_name' => 'Cursor',
                'model_permaslug' => 'openai/gpt-4o',
                'turn_range' => '1-5',
                'median_session_cost_usd' => 0.031,
            ]],
            'meta' => [],
        ]);

        $datasets = $this->client($http)->datasets();

        $daily = $datasets->dailyRankings(startDate: '2026-08-01', endDate: '2026-09-01');
        $this->assertStringContainsString('/datasets/rankings-daily', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(DailyRankingsResponse::class, $daily);
        $this->assertSame('2026-09-01', $daily->data[0]->date);
        $this->assertSame(999, $daily->data[0]->totalTokens);

        $cost = $datasets->sessionCost(appSlug: 'cursor');
        $this->assertStringContainsString('/datasets/session-cost', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(SessionCostResponse::class, $cost);
        $this->assertSame('1-5', $cost->data[0]->turnRange);
        $this->assertSame(0.031, $cost->data[0]->medianSessionCostUsd);
    }

    public function testBenchmarksListAndTaskClassification(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [['name' => 'SWE-bench', 'score' => 0.71]], 'meta' => ['count' => 1]]);
        $http->enqueueJson(['data' => ['as_of' => '2026-06-17', 'tasks' => []]]);

        $benchmarks = $this->client($http)->benchmarks();

        $list = $benchmarks->list(taskType: 'coding', maxResults: 5);
        $query = [];
        parse_str($http->lastRequest()->getUri()->getQuery(), $query);
        $this->assertStringContainsString('/benchmarks', (string) $http->lastRequest()->getUri());
        $this->assertSame('coding', $query['task_type']);
        $this->assertSame('5', $query['max_results']);
        $this->assertInstanceOf(ListBenchmarksResponse::class, $list);
        $this->assertSame('SWE-bench', $list->data[0]['name']);
        $this->assertSame(['count' => 1], $list->metadata);

        $classification = $benchmarks->taskClassification(window: '30d');
        $this->assertStringContainsString('/classifications/task', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(DataResponse::class, $classification);
        $this->assertSame('2026-06-17', $classification->data['as_of']);
    }
}
