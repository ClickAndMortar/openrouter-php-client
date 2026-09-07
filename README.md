<p align="center">
    <p align="center">
        <a href="https://github.com/ClickAndMortar/openrouter-php-client/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/ClickAndMortar/openrouter-php-client/ci.yml?branch=main&label=tests&style=round-square"></a>
        <a href="https://packagist.org/packages/ClickAndMortar/openrouter-php-client"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/ClickAndMortar/openrouter-php-client"></a>
        <a href="https://packagist.org/packages/ClickAndMortar/openrouter-php-client"><img alt="Latest Version" src="https://img.shields.io/packagist/v/ClickAndMortar/openrouter-php-client"></a>
        <a href="https://packagist.org/packages/ClickAndMortar/openrouter-php-client"><img alt="License" src="https://img.shields.io/github/license/ClickAndMortar/openrouter-php-client"></a>
    </p>
</p>

------

# OpenRouter PHP Client

A PHP SDK for [OpenRouter](https://openrouter.ai) with typed request/response value objects, SSE streaming, and PSR-18 HTTP transport. The API mirrors the ergonomics of [`openai-php/client`](https://github.com/openai-php/client).

Alongside full coverage of the OpenRouter REST API, the SDK includes [agentic helpers](#agentic-helpers) that make working with LLMs easier: define your tools as plain PHP closures, call `->run()`, and the client takes care of the back-and-forth with the model — calling your tools, feeding results back, and looping until the model is done — so you get a final answer without writing the glue code yourself.

## Requirements

- PHP 8.2+
- A PSR-18 HTTP client (auto-discovered via `php-http/discovery`). Guzzle is auto-detected and configured for non-buffering streams; `symfony/http-client` (via `Psr18Client`) and `php-http/curl-client` stream by default as well. Other PSR-18 clients may buffer the full response before the iterator begins - if yours does, supply a streaming closure via `Factory::withStreamHandler()`.

## Installation

```bash
composer require clickandmortar/openrouter-php-client
```

Coming from a 0.x release? See [UPGRADE.md](UPGRADE.md) — most upgrades need no code
changes. Release history lives in [CHANGELOG.md](CHANGELOG.md).

## Quick start

```php
use OpenRouter\OpenRouter;

$client = OpenRouter::client($_ENV['OPENROUTER_API_KEY']);

$result = $client->chat()->send([
    'model' => 'openai/gpt-4o',
    'messages' => [
        ['role' => 'system', 'content' => 'You are helpful.'],
        ['role' => 'user', 'content' => 'Hello!'],
    ],
]);

echo $result->choices[0]->message->content;
// Or use the convenience accessor — flattens content parts, null-safe:
echo $result->text();
```

> **Agentic tool loops:** prefer `$client->chat()->agent()` / `$client->responses()->agent()` over hand-rolled tool-call plumbing. See [Agentic helpers](#agentic-helpers) below.

## Endpoint coverage

Every endpoint in the OpenRouter OpenAPI spec has a typed wrapper — 106 of 106 operations:

| Endpoint                                      | Method               | Status | SDK call                                                |
|-----------------------------------------------|----------------------|:------:|---------------------------------------------------------|
| `/chat/completions`                           | POST                 |   ✅    | `$client->chat()->send(...)` / `sendStreamed(...)`      |
| `/responses`                                  | POST                 |   ✅    | `$client->responses()->send(...)` / `sendStreamed(...)` |
| `/models/user`                                | GET                  |   ✅    | `$client->models()->listForUser()`                      |
| `/models`                                     | GET                  |   ✅    | `$client->models()->list(...)`                          |
| `/models/count`                               | GET                  |   ✅    | `$client->models()->count(...)`                         |
| `/models/{author}/{slug}/endpoints`           | GET                  |   ✅    | `$client->models()->listEndpoints($author, $slug)`      |
| `/messages`                                   | POST                 |   ✅    | `$client->messages()->send(...)` / `sendStreamed(...)`  |
| `/embeddings`                                 | POST                 |   ✅    | `$client->embeddings()->generate(...)`                  |
| `/embeddings/models`                          | GET                  |   ✅    | `$client->embeddings()->listModels()`                   |
| `/rerank`                                     | POST                 |   ✅    | `$client->rerank()->rerank(...)`                        |
| `/images`                                     | POST                 |   ✅    | `$client->images()->generate(...)` / `generateStreamed(...)` |
| `/images/models`                              | GET                  |   ✅    | `$client->images()->listModels()`                       |
| `/images/models/{author}/{slug}/endpoints`    | GET                  |   ✅    | `$client->images()->listEndpoints($author, $slug)`      |
| `/videos`                                     | POST                 |   ✅    | `$client->videos()->generate(...)`                      |
| `/videos/{jobId}`                             | GET                  |   ✅    | `$client->videos()->retrieve($jobId)`                   |
| `/videos/{jobId}/content`                     | GET                  |   ✅    | `$client->videos()->download($jobId)`                   |
| `/videos/models`                              | GET                  |   ✅    | `$client->videos()->listModels()`                       |
| `/audio/speech`                               | POST                 |   ✅    | `$client->audio()->speech(...)`                         |
| `/audio/transcriptions`                       | POST                 |   ✅    | `$client->audio()->transcribe(...)` / `transcribeFile(...)` |
| `/files`                                      | GET / POST           |   ✅    | `$client->files()->list(...)` / `upload(...)`           |
| `/files/{file_id}`                            | GET / DELETE         |   ✅    | `$client->files()->retrieve($id)` / `delete($id)`       |
| `/files/{file_id}/content`                    | GET                  |   ✅    | `$client->files()->download($id)`                       |
| `/containers/{id}/files`                      | GET                  |   ✅    | `$client->containers()->listFiles($containerId)`        |
| `/containers/{id}/files/{file_id}`            | GET                  |   ✅    | `$client->containers()->retrieveFile($containerId, $fileId)` |
| `/containers/{id}/files/{file_id}/content`    | GET                  |   ✅    | `$client->containers()->downloadFile($containerId, $fileId)` |
| `/containers/{id}/files/{file_id}/promote`    | POST                 |   ✅    | `$client->containers()->promoteFile($containerId, $fileId)` |
| `/workspaces`                                 | GET / POST           |   ✅    | `$client->workspaces()->list(...)` / `create(...)`      |
| `/workspaces/{id}`                            | GET / PATCH / DELETE |   ✅    | `$client->workspaces()->retrieve($id)` / `update(...)` / `delete($id)` |
| `/workspaces/{id}/budgets`                    | GET                  |   ✅    | `$client->workspaces()->listBudgets($id)`               |
| `/workspaces/{id}/budgets/{interval}`         | GET / PUT / DELETE   |   ✅    | `$client->workspaces()->retrieveBudget(...)` / `setBudget(...)` / `deleteBudget(...)` |
| `/workspaces/{id}/members`                    | GET                  |   ✅    | `$client->workspaces()->listMembers($id)`               |
| `/workspaces/{id}/members/add`                | POST                 |   ✅    | `$client->workspaces()->addMembers($id, $userIds)`      |
| `/workspaces/{id}/members/remove`             | POST                 |   ✅    | `$client->workspaces()->removeMembers($id, $userIds)`   |
| `/presets`                                    | GET                  |   ✅    | `$client->presets()->list(...)`                         |
| `/presets/{slug}`                             | GET                  |   ✅    | `$client->presets()->retrieve($slug)`                   |
| `/presets/{slug}/versions`                    | GET                  |   ✅    | `$client->presets()->listVersions($slug)`               |
| `/presets/{slug}/versions/{version}`          | GET                  |   ✅    | `$client->presets()->retrieveVersion($slug, $version)`  |
| `/presets/{slug}/chat/completions`            | POST                 |   ✅    | `$client->presets()->createFromChat($slug, ...)`        |
| `/presets/{slug}/messages`                    | POST                 |   ✅    | `$client->presets()->createFromMessages($slug, ...)`    |
| `/presets/{slug}/responses`                   | POST                 |   ✅    | `$client->presets()->createFromResponses($slug, ...)`   |
| `/byok`                                       | GET / POST           |   ✅    | `$client->byok()->list(...)` / `create(...)`            |
| `/byok/{id}`                                  | GET / PATCH / DELETE |   ✅    | `$client->byok()->retrieve($id)` / `update(...)` / `delete($id)` |
| `/model/{author}/{slug}`                      | GET                  |   ✅    | `$client->models()->retrieve($author, $slug)`           |
| `/generation/content`                         | GET                  |   ✅    | `$client->generation()->content($id)`                   |
| `/generation/feedback`                        | POST                 |   ✅    | `$client->generation()->submitFeedback(...)`            |
| `/analytics/meta`                             | GET                  |   ✅    | `$client->analytics()->meta()`                          |
| `/analytics/query`                            | POST                 |   ✅    | `$client->analytics()->query(...)`                      |
| `/benchmarks`                                 | GET                  |   ✅    | `$client->benchmarks()->list(...)`                      |
| `/classifications/task`                       | GET                  |   ✅    | `$client->benchmarks()->taskClassification(...)`        |
| `/datasets/app-rankings`                      | GET                  |   ✅    | `$client->datasets()->appRankings(...)`                 |
| `/datasets/rankings-daily`                    | GET                  |   ✅    | `$client->datasets()->dailyRankings(...)`               |
| `/datasets/session-cost`                      | GET                  |   ✅    | `$client->datasets()->sessionCost(...)`                 |
| `/observability/destinations`                 | GET / POST           |   ✅    | `$client->observability()->list(...)` / `create(...)`   |
| `/observability/destinations/{id}`            | GET / PATCH / DELETE |   ✅    | `$client->observability()->retrieve($id)` / `update(...)` / `delete($id)` |
| `/scim/groups`                                | GET                  |   ✅    | `$client->scim()->listGroups(...)`                      |
| `/scim/group-mappings`                        | GET / POST           |   ✅    | `$client->scim()->listGroupMappings(...)` / `createGroupMapping(...)` |
| `/scim/group-mappings/{id}`                   | GET / PATCH / DELETE |   ✅    | `$client->scim()->retrieveGroupMapping($id)` / `updateGroupMapping(...)` / `deleteGroupMapping(...)` |
| `/scim/sync-jobs`                             | POST                 |   ✅    | `$client->scim()->createSyncJob()`                      |
| `/scim/sync-jobs/{id}`                        | GET                  |   ✅    | `$client->scim()->retrieveSyncJob($id)`                 |
| `/generation`                                 | GET                  |   ✅    | `$client->generation()->retrieve($id)`                  |
| `/activity`                                   | GET                  |   ✅    | `$client->activity()->list(...)`                        |
| `/credits`                                    | GET                  |   ✅    | `$client->credits()->retrieve()`                        |
| `/credits/coinbase`                           | POST                 |   ⚠️    | `$client->credits()->createCoinbaseCharge()` (deprecated - returns HTTP 410) |
| `/key`                                        | GET                  |   ✅    | `$client->keys()->current()`                            |
| `/keys`                                       | GET / POST           |   ✅    | `$client->keys()->list(...)` / `create(...)`            |
| `/keys/{hash}`                                | GET / PATCH / DELETE |   ✅    | `$client->keys()->retrieve($hash)` / `update(...)` / `delete($hash)` |
| `/auth/keys`                                  | POST                 |   ✅    | `$client->auth()->exchangeCode(...)`                    |
| `/auth/keys/code`                             | POST                 |   ✅    | `$client->auth()->createAuthCode(...)`                  |
| `/oauth/token`                                | POST                 |   ✅    | `$client->oauth()->exchangeToken(...)`                  |
| `/oauth/jwks`                                 | GET                  |   ✅    | `$client->oauth()->jwks()`                              |
| `/providers`                                  | GET                  |   ✅    | `$client->providers()->list()`                          |
| `/endpoints/zdr`                              | GET                  |   ✅    | `$client->endpoints()->listZdr()`                       |
| `/organization/members`                       | GET                  |   ✅    | `$client->organization()->listMembers(...)`             |
| `/organization`                               | POST                 |   ✅    | `$client->organization()->create(...)`                  |
| `/guardrails`                                 | GET / POST           |   ✅    | `$client->guardrails()->list(...)` / `create(...)`      |
| `/guardrails/{id}`                            | GET / PATCH / DELETE |   ✅    | `$client->guardrails()->retrieve($id)` / `update(...)` / `delete($id)` |
| `/guardrails/{id}/assignments/keys`           | GET / POST           |   ✅    | `$client->guardrails()->listKeyAssignments($id, ...)` / `bulkAssignKeys($id, $hashes)` |
| `/guardrails/{id}/assignments/keys/remove`    | POST                 |   ✅    | `$client->guardrails()->bulkUnassignKeys($id, $hashes)` |
| `/guardrails/{id}/assignments/members`        | GET / POST           |   ✅    | `$client->guardrails()->listMemberAssignments($id, ...)` / `bulkAssignMembers($id, $userIds)` |
| `/guardrails/{id}/assignments/members/remove` | POST                 |   ✅    | `$client->guardrails()->bulkUnassignMembers($id, $userIds)` |
| `/guardrails/assignments/keys`                | GET                  |   ✅    | `$client->guardrails()->listAllKeyAssignments(...)`     |
| `/guardrails/assignments/members`             | GET                  |   ✅    | `$client->guardrails()->listAllMemberAssignments(...)`  |

Unsupported endpoints can still be reached through `$client->transporter()` - build a `Payload` and dispatch it manually. PRs adding typed wrappers are welcome.

`api-coverage.json` tracks every operation in the live spec and CI fails when a new one appears upstream; see [Staying in sync with the API](#staying-in-sync-with-the-api). Per-release changes are in [CHANGELOG.md](CHANGELOG.md).

```php
use OpenRouter\ValueObjects\Transporter\Payload;

$response = $client->transporter()->requestObject(
    Payload::list('organization/members'),
);

$members = $response->data();
```

The transporter handles three body shapes, so the escape hatch reaches every endpoint:

```php
use OpenRouter\ValueObjects\Transporter\Payload;
use OpenRouter\ValueObjects\Transporter\UploadedFile;

// JSON in, JSON out
$client->transporter()->requestObject(Payload::create('some/endpoint', ['k' => 'v']));

// multipart/form-data upload
$client->transporter()->requestObject(Payload::upload('some/endpoint', [
    'file' => UploadedFile::fromPath('/path/to/file.bin'),
    'model' => 'openai/whisper-1',
]));

// a non-JSON body (audio, video, a file download) — returned as raw bytes
$binary = $client->transporter()->requestContent(Payload::list('some/endpoint/content'));
$binary->saveTo('out.bin');
```

### Filtering and paginating models

`GET /models` accepts far more query parameters than it used to. The common ones are named arguments; everything else goes through `$filters`, and the response carries the pagination cursor:

```php
$page = $client->models()->list(
    category: 'programming',
    filters: [
        'limit' => 50,
        'offset' => 0,
        'q' => 'claude',
        'sort' => 'newest',
        'max_price' => 0.5,
        'min_intelligence_index' => 60,
        'providers' => 'anthropic',
        'zdr' => 'true',
    ],
);

echo $page->totalCount;  // total matches across all pages
echo $page->nextPage;    // URL of the next page, or null on the last one
```

`$client->models()->listForUser(limit: 25, offset: 0)` and `$client->embeddings()->listModels(limit: 25, offset: 0)` paginate the same way.

## Chat completions

### Typed requests

```php
use OpenRouter\OpenRouter;
use OpenRouter\ValueObjects\Chat\CreateChatRequest;
use OpenRouter\ValueObjects\Chat\Messages\{SystemMessage, UserMessage};
use OpenRouter\ValueObjects\Chat\Content\{ChatTextPart, ChatImagePart};

$client = OpenRouter::client($_ENV['OPENROUTER_API_KEY']);

$result = $client->chat()->send(new CreateChatRequest(
    model: 'openai/gpt-4o',
    messages: [
        new SystemMessage('You are a helpful assistant.'),
        new UserMessage([
            new ChatTextPart('What is in this image?'),
            new ChatImagePart(url: 'https://example.com/cat.jpg'),
        ]),
    ],
    temperature: 0.7,
    maxCompletionTokens: 256,
));

$result->choices[0]->message->content;
$result->usage->promptTokens;
$result->usage->cost;
```

#### Sampling, caching and agent-loop controls

```php
use OpenRouter\Enums\Responses\ReasoningEffort;

new CreateChatRequest(
    model: 'openai/gpt-4o',
    messages: [new UserMessage('Refactor this module.')],

    // OpenRouter sampling parameters
    topK: 40,
    minP: 0.1,
    topA: 0.2,
    repetitionPenalty: 1.1,

    // Shorthand for reasoning.effort
    reasoningEffort: ReasoningEffort::Max,

    // Speculative decoding when much of the answer is known up front
    prediction: ['type' => 'content', 'content' => $currentFileContents],

    // Prompt caching
    promptCacheKey: 'module-refactor-v1',
    promptCacheOptions: ['mode' => 'explicit', 'ttl' => '30m'],

    // Halt the server-tool agent loop (OR logic across conditions)
    stopServerToolsWhen: [
        ['type' => 'step_count_is', 'step_count' => 5],
        ['type' => 'max_cost', 'max_cost_in_dollars' => 0.5],
    ],
);
```

### Streaming

```php
$stream = $client->chat()->sendStreamed(new CreateChatRequest(
    model: 'openai/gpt-4o',
    messages: [new UserMessage('Write a haiku.')],
));

foreach ($stream as $chunk) {
    echo $chunk->choices[0]->delta->content ?? '';
}
```

The final chunk carries `usage` and a non-null `finish_reason`.

Streaming relies on the underlying PSR-18 client returning a non-buffered body. Guzzle is detected and sent with `['stream' => true]` automatically; Symfony `Psr18Client` and `php-http/curl-client` stream by default. For other PSR-18 clients that buffer responses, pass a custom closure via `Factory::withStreamHandler(fn ($req) => ...)` that issues the request with streaming enabled and returns a `Psr\Http\Message\ResponseInterface` whose body reads lazily.

### Tool calling

```php
use OpenRouter\ValueObjects\Chat\Tools\ChatFunctionTool;
use OpenRouter\ValueObjects\Chat\Config\ChatToolChoice;
use OpenRouter\ValueObjects\Chat\Messages\{AssistantMessage, ToolMessage};
use OpenRouter\ValueObjects\Chat\Tools\ChatToolCallRequest;

$result = $client->chat()->send(new CreateChatRequest(
    model: 'openai/gpt-4o',
    messages: [new UserMessage('Weather in Paris?')],
    tools: [
        new ChatFunctionTool(
            name: 'get_weather',
            parameters: [
                'type' => 'object',
                'properties' => ['location' => ['type' => 'string']],
                'required' => ['location'],
            ],
        ),
    ],
    toolChoice: ChatToolChoice::auto(),
));

$call = $result->choices[0]->message->toolCalls[0];
$args = json_decode($call->functionArguments, true);
$weather = lookup_weather($args['location']);

// Continue the conversation with the tool result:
$followup = $client->chat()->send(new CreateChatRequest(
    model: 'openai/gpt-4o',
    messages: [
        new UserMessage('Weather in Paris?'),
        new AssistantMessage(toolCalls: [
            new ChatToolCallRequest($call->id, $call->functionName, $call->functionArguments),
        ]),
        new ToolMessage(content: $weather, toolCallId: $call->id),
    ],
));
```

### Structured output

```php
use OpenRouter\ValueObjects\Chat\Config\JsonSchemaResponseFormat;

$result = $client->chat()->send(new CreateChatRequest(
    model: 'openai/gpt-4o',
    messages: [new UserMessage('Extract: "John is 30 years old."')],
    responseFormat: new JsonSchemaResponseFormat(
        name: 'person',
        schema: [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
            ],
            'required' => ['name', 'age'],
        ],
        strict: true,
    ),
));
```

## Messages (Anthropic format)

OpenRouter's Anthropic-compatible `/messages` endpoint. Same SSE plumbing as `/chat/completions`, but the request and response shapes follow Anthropic's content-block format.

### Quick example

```php
$result = $client->messages()->send([
    'model' => 'anthropic/claude-sonnet-4',
    'max_tokens' => 1024,
    'messages' => [
        ['role' => 'user', 'content' => 'Hello, how are you?'],
    ],
]);

$result->content[0]->text;      // "I'm doing well, thank you..."
$result->stopReason;            // 'end_turn'
$result->usage->inputTokens;    // 12
$result->usage->outputTokens;   // 18
```

### Typed requests

Every nested discriminated union is modeled (content blocks, tools, tool_choice, thinking, context_management, citations, plugins, output_config). Raw arrays still work for any field.

```php
use OpenRouter\ValueObjects\Messages\CreateMessagesRequest;
use OpenRouter\ValueObjects\Messages\Messages\{UserMessage, AssistantMessage};
use OpenRouter\ValueObjects\Messages\Content\{TextBlock, ImageBlock, ToolUseBlock, ToolResultBlock, MessagesCacheControl};
use OpenRouter\ValueObjects\Messages\Tools\{CustomTool, WebSearchTool, BashTool};
use OpenRouter\ValueObjects\Messages\Config\{MessagesToolChoice, MessagesThinkingConfig, MessagesOutputConfig};

$result = $client->messages()->send(new CreateMessagesRequest(
    model: 'anthropic/claude-sonnet-4',
    maxTokens: 1024,
    system: [new TextBlock('You are helpful.', cacheControl: new MessagesCacheControl(ttl: '1h'))],
    messages: [
        new UserMessage([
            new TextBlock('What is in this image?'),
            ImageBlock::url('https://example.com/cat.jpg'),
        ]),
    ],
    tools: [
        new CustomTool(
            name: 'get_weather',
            inputSchema: [
                'type' => 'object',
                'properties' => ['location' => ['type' => 'string']],
                'required' => ['location'],
            ],
        ),
        new WebSearchTool(),
        new BashTool(),
    ],
    toolChoice: MessagesToolChoice::auto(disableParallelToolUse: true),
    thinking: MessagesThinkingConfig::enabled(budgetTokens: 2048),
));
```

### Tool calling

Multi-turn tool-use round-trips through typed content blocks:

```php
use OpenRouter\ValueObjects\Messages\Content\{ToolUseBlock, ToolResultBlock};

// First turn: model requests a tool call
$first = $client->messages()->send(new CreateMessagesRequest(
    model: 'anthropic/claude-sonnet-4',
    maxTokens: 1024,
    messages: [new UserMessage('Weather in Paris?')],
    tools: [new CustomTool(name: 'get_weather', inputSchema: [...])],
));

$toolUse = $first->content[1]; // ToolUseBlock
$weather = lookup_weather($toolUse->input['location']);

// Second turn: replay assistant's tool_use + our tool_result
$final = $client->messages()->send(new CreateMessagesRequest(
    model: 'anthropic/claude-sonnet-4',
    maxTokens: 1024,
    messages: [
        new UserMessage('Weather in Paris?'),
        new AssistantMessage([$toolUse]),
        new UserMessage([
            new ToolResultBlock(toolUseId: $toolUse->id, content: $weather),
        ]),
    ],
));
```

### Streaming

Every SSE frame yields a typed subclass of `MessagesStreamEvent` - one per documented Anthropic event type (`message_start`, `content_block_start`, `content_block_delta`, `content_block_stop`, `message_delta`, `message_stop`, `ping`, `error`). Deltas and content blocks are also typed.

```php
use OpenRouter\Responses\Messages\Stream\MessagesContentBlockDeltaEvent;
use OpenRouter\Responses\Messages\Deltas\{TextDelta, InputJsonDelta};

$stream = $client->messages()->sendStreamed(new CreateMessagesRequest(
    model: 'anthropic/claude-sonnet-4',
    maxTokens: 1024,
    messages: [new UserMessage('Write a haiku.')],
));

$text = '';
$toolArgs = '';
foreach ($stream as $event) {
    if ($event instanceof MessagesContentBlockDeltaEvent) {
        if ($event->delta instanceof TextDelta) {
            $text .= $event->delta->text;
        } elseif ($event->delta instanceof InputJsonDelta) {
            $toolArgs .= $event->delta->partialJson; // concat to reconstruct tool input
        }
    }
}
```

### Extended thinking, caching, context management

```php
use OpenRouter\ValueObjects\Messages\ContextManagement\{ContextManagement, ClearThinkingEdit, CompactEdit};

new CreateMessagesRequest(
    model: 'anthropic/claude-sonnet-4',
    maxTokens: 8192,
    messages: [new UserMessage('Solve this...')],
    thinking: MessagesThinkingConfig::enabled(budgetTokens: 4096),
    cacheControl: new MessagesCacheControl(ttl: '1h'),
    contextManagement: new ContextManagement([
        ClearThinkingEdit::keepTurns(3),
        new CompactEdit(instructions: 'summarize aggressively'),
    ]),
    outputConfig: MessagesOutputConfig::jsonSchema([
        'type' => 'object',
        'properties' => ['answer' => ['type' => 'string']],
    ], effort: 'high'),
);
```

## Responses API

```php
$response = $client->responses()->send([
    'model' => 'openai/gpt-4o',
    'input' => 'Tell me a joke',
]);

$response->output[0]->content[0]->text;
$response->usage->totalTokens;

foreach ($client->responses()->sendStreamed([...]) as $event) {
    // typed CreateStreamedResponse subclass per SSE frame
}
```

## Agentic helpers

The SDK ships a small agent runner that removes the boilerplate around tool loops and output extraction — inspired by OpenRouter's TypeScript [`callModel`](https://openrouter.ai/docs/sdks/typescript/call-model/overview) API. Available for both `/chat/completions` (`$client->chat()->agent()`) and `/responses` (`$client->responses()->agent()`).

### Result accessors

Both `ChatResult` and `CreateResponse` expose shortcut methods so you don't have to walk the raw structure:

```php
$result = $client->chat()->send(new CreateChatRequest(/* ... */));

$result->text();          // Final assistant text, flattens text content-parts, null-safe
$result->toolCalls();     // list<ChatToolCall>
$result->finishReason();  // 'stop' | 'tool_calls' | 'length' | ...
$result->refusal();       // model-produced refusal string, or null
$result->reasoning();     // assistant reasoning trace, or null

foreach ($result->toolCalls() as $call) {
    $args = $call->arguments(); // JSON-decoded, memoised — no manual json_decode
}
```

```php
$response = $client->responses()->send(new CreateResponseRequest(/* ... */));

$response->text();                      // Prefers server output_text, else joins output_text parts
$response->toolCalls();                 // list<CreateResponseOutputFunctionCall>
$response->functionCall('get_weather'); // first function-call by name, or null
$response->messages();                  // typed message output items
$response->reasoning();                 // typed reasoning output items

foreach ($response->toolCalls() as $call) {
    $args = $call->decodedArguments();  // memoised
}
```

### OpenRouter server tools

OpenRouter hosts a family of `openrouter:*` server tools it runs on your behalf - no executor closure needed. They share one `{type, parameters}` envelope, so a single value object per endpoint covers all of them, with named constructors for discoverability:

```php
use OpenRouter\ValueObjects\Responses\Tools\OpenRouterServerTool;

$response = $client->responses()->send([
    'model' => 'openai/gpt-4o',
    'input' => 'Summarise https://example.com and patch the README accordingly.',
    'tools' => [
        OpenRouterServerTool::webFetch(['max_uses' => 3, 'allowed_domains' => ['example.com']]),
        OpenRouterServerTool::applyPatch(),
        OpenRouterServerTool::bash(['engine' => 'container']),
    ],
]);
```

| Endpoint            | Value object                    | Types                                                                                                                                      |
|---------------------|---------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------|
| `/responses`        | `OpenRouterServerTool`          | `advisor`, `apply_patch`, `bash`, `experimental__search_models`, `files`, `fusion`, `image_generation`, `shell`, `subagent`, `tool_search`, `web_fetch` |
| `/chat/completions` | `ChatOpenRouterServerTool`      | `advisor`, `bash`, `experimental__search_models`, `files`, `fusion`, `image_generation`, `subagent`, `web_fetch`                            |
| `/messages`         | `MessagesOpenRouterServerTool`  | `bash`, `experimental__search_models`, `image_generation`, `shell`, `tool_search`, `web_fetch`                                              |

`openrouter:web_search` and `openrouter:datetime` predate this family and keep their own value objects. `NamespaceTool` groups several tools behind one named entry so the model sees a single tool.

### Plugins

```php
use OpenRouter\ValueObjects\Responses\Plugins\FusionPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\ParetoRouterPlugin;

$client->chat()->send([
    'model' => 'openai/gpt-4o',
    'messages' => [['role' => 'user', 'content' => 'Hi']],
    'plugins' => [
        new FusionPlugin(enabled: true, preset: 'general-high'),
    ],
]);
```

Available: `web`, `web-fetch`, `file-parser`, `moderation`, `response-healing`, `context-compression`, `auto-router`, `auto-beta-router`, `pareto-router`, `fusion`.

### Agent runner — multi-turn tool loops

Register tools with their executor closures, call `->run()`, get a final answer. The runner handles the model → tool call → tool result → model loop for you, up to `maxToolRounds`.

```php
use OpenRouter\Agent\AgentTool;

$result = $client->chat()->agent()
    ->model('openai/gpt-4o')
    ->system('You are a travel assistant.')
    ->user('What is the weather in Paris?')
    ->tool(AgentTool::define(
        name: 'get_weather',
        execute: fn (array $args) => $weatherService->fetch($args['city']),
        description: 'Get the current weather for a city.',
        parameters: [
            'type' => 'object',
            'properties' => ['city' => ['type' => 'string']],
            'required' => ['city'],
        ],
    ))
    ->maxToolRounds(5) // or a predicate: fn (int $turn, AgentStep $last) => $turn < 5
    ->run();

echo $result->text();      // Final assistant answer after the tool loop
$result->steps();          // list<AgentStep> — one per request/tool-exec round
$result->finalResponse;    // underlying ChatResult for escape-hatch access
```

The `/responses` agent has the same API and uses `previous_response_id` for state chaining when the server returns one:

```php
$result = $client->responses()->agent()
    ->model('openai/gpt-4o')
    ->user('Book me a flight to Tokyo next week.')
    ->tool(AgentTool::define(name: 'search_flights', execute: $searchFlights))
    ->tool(AgentTool::define(name: 'book_flight',   execute: $bookFlight))
    ->run();

echo $result->text();
```

### Knobs

| Method | Purpose |
|---|---|
| `->maxToolRounds(int\|Closure)` | Cap the loop. `0` disables auto-execution — raw tool calls come back in `$result->toolCalls()`. A closure `fn(int $turn, AgentStep $last): bool` gives dynamic control. |
| `->throwOnMaxRounds(bool)` | Default true. When false, hitting the cap returns an `AgentRunResult` with `stoppedOnMaxRounds === true` instead of throwing. |
| `->rethrowToolExceptions(bool)` | Default false: tool handler exceptions are serialised as `{"error": "..."}` and fed back to the model so it can recover. Set true to bubble them up. |
| `->parallelToolCalls(bool)` / `->toolChoice(...)` / `->temperature(...)` / `->topP(...)` / `->maxTokens(...)` / `->responseFormat(...)` (chat) / `->maxOutputTokens(...)` / `->maxToolCalls(...)` / `->instructions(...)` (responses) | Pass-through to the underlying request VO. |
| `->extra(array)` | Merge additional raw request fields (seed, provider, etc.) — forwarded via the `extras` bag. |

`AgentTool::define()` takes a closure with the signature `fn(array $decodedArgs, AgentToolContext $ctx): mixed`. Non-string returns are `json_encode`d before being sent back to the model. The `AgentToolContext` exposes the current `turn`, the `toolCallId`, and the `toolName`.

### Class-based tools

For stateful tools, dependency-injected services, or tools you want to unit-test on their own, implement the `AgentToolDefinition` interface and hand instances to `->tool()` just like `AgentTool` closures — the runner accepts both.

```php
use OpenRouter\Agent\AgentToolContext;
use OpenRouter\Agent\AgentToolDefinition;

final class GetWeatherTool implements AgentToolDefinition
{
    public function __construct(private readonly WeatherService $weather) {}

    public function name(): string { return 'get_weather'; }
    public function description(): ?string { return 'Get the current weather for a city.'; }
    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => ['city' => ['type' => 'string']],
            'required' => ['city'],
        ];
    }
    public function strict(): ?bool { return null; }

    public function execute(array $arguments, AgentToolContext $context): mixed
    {
        return $this->weather->fetch($arguments['city']);
    }
}

$result = $client->chat()->agent()
    ->model('openai/gpt-4o')
    ->user('What is the weather in Paris?')
    ->tool(new GetWeatherTool($weatherService))
    ->run();
```

## Images

```php
use OpenRouter\ValueObjects\Images\CreateImageRequest;

$result = $client->images()->generate(new CreateImageRequest(
    model: 'openai/gpt-image-1',
    prompt: 'A cat surfing a wave, watercolour',
    n: 1,
    size: '1024x1024',
    outputFormat: 'png',
));

file_put_contents('cat.png', $result->data[0]->binary());   // base64 decoded for you
echo $result->usage?->cost;
```

Streaming yields progressive previews plus any text the model emits while it works. Only models whose catalogue entry reports `supports_streaming` accept it:

```php
foreach ($client->images()->generateStreamed([
    'model' => 'openai/gpt-image-1',
    'prompt' => 'A cat surfing',
]) as $event) {
    match (true) {
        $event instanceof ImageStreamTextChunkEvent    => print($event->text),
        $event instanceof ImageStreamPartialImageEvent => savePreview($event->binary(), $event->partialImageIndex),
        $event instanceof ImageStreamCompletedEvent    => file_put_contents('final.png', $event->binary()),
        default => null,   // unknown frames fall back to ImageStreamEvent
    };
}
```

`$client->images()->listModels()` and `->listEndpoints($author, $slug)` describe which models exist and what each provider endpoint supports.

## Videos

Video generation is asynchronous — submit, poll, download:

```php
use OpenRouter\ValueObjects\Videos\CreateVideoRequest;

$job = $client->videos()->generate(new CreateVideoRequest(
    model: 'google/veo-3',
    prompt: 'A timelapse of a city at dusk',
    duration: 8,
    aspectRatio: '16:9',
    generateAudio: true,
));

do {
    sleep(5);
    $job = $client->videos()->retrieve($job->id);
} while (! $job->isTerminal());

$client->videos()->download($job->id)->saveTo('city.mp4');
```

`$client->videos()->listModels()` reports each model's supported resolutions, aspect ratios and durations, and whether it can generate audio.

## Audio

```php
use OpenRouter\ValueObjects\Audio\CreateSpeechRequest;

$speech = $client->audio()->speech(new CreateSpeechRequest(
    model: 'openai/tts-1',
    input: 'Hello from OpenRouter.',
    voice: 'alloy',
    responseFormat: 'mp3',
));

$speech->saveTo('hello.mp3');
echo $speech->contentType;   // audio/mpeg
```

Transcription accepts either an uploaded file or inline audio:

```php
use OpenRouter\ValueObjects\Transporter\UploadedFile;

$transcription = $client->audio()->transcribeFile(
    UploadedFile::fromPath('/path/to/interview.wav'),
    'openai/whisper-1',
    ['language' => 'en', 'timestamp_granularities' => ['word', 'segment']],
);

echo $transcription->text;
foreach ($transcription->segments as $segment) {
    printf("[%.2f-%.2f] %s\n", $segment->start, $segment->end, $segment->text);
}
```

## Files

```php
use OpenRouter\ValueObjects\Transporter\UploadedFile;

$file = $client->files()->upload(UploadedFile::fromPath('/path/to/report.pdf'))->data;

$client->files()->list(limit: 20)->data;
$client->files()->retrieve($file->id)->data->sizeInBytes();
$client->files()->download($file->id)->saveTo('copy.pdf');
$client->files()->delete($file->id);
```

Pass `provider: 'openai'` (or `'anthropic'`) to store the file with that provider under your own key instead of on OpenRouter. The payload shape is negotiated per request and named by `_shape`; `StoredFile::sizeInBytes()` reads the size without branching on it.

## Container files

Files a code-interpreter run produced, and promoting one into durable workspace documents:

```php
$files = $client->containers()->listFiles($containerId)->data;

$client->containers()->downloadFile($containerId, $files[0]->id)->saveTo('chart.png');

$stored = $client->containers()->promoteFile($containerId, $files[0]->id)->data;
```

## Workspaces

A workspace scopes keys, guardrails, budgets and members. Most account-level calls take a `workspace_id`.

```php
use OpenRouter\Enums\Workspaces\BudgetInterval;
use OpenRouter\ValueObjects\Workspaces\CreateWorkspaceRequest;

$workspace = $client->workspaces()->create(new CreateWorkspaceRequest(
    name: 'Platform team',
    slug: 'platform-team',
    defaultTextModel: 'openai/gpt-4o',
))->data;

// One budget per interval — setBudget() is an upsert
$client->workspaces()->setBudget($workspace->id, BudgetInterval::Monthly, limitUsd: 250.0);
$client->workspaces()->listBudgets($workspace->id)->data;

$client->workspaces()->addMembers($workspace->id, ['user_1', 'user_2']);
$client->workspaces()->listMembers($workspace->id)->data;
```

Deleting the default workspace is guarded: pass `confirmDefaultWorkspaceDeletion: true` to go through with it.

## Presets

A preset is a saved inference configuration addressed by slug. Sending a request to a preset-scoped endpoint records that request's settings as a new version:

```php
$client->presets()->createFromChat('support-agent', new CreateChatRequest(
    model: 'openai/gpt-4o',
    messages: [new SystemMessage('You are a support agent.')],
    temperature: 0.2,
));

$preset = $client->presets()->retrieve('support-agent')->data;
$versions = $client->presets()->listVersions('support-agent')->data;

echo $versions[0]->version;        // 3
echo $versions[0]->systemPrompt;
print_r($versions[0]->config);     // the settings as they were sent
```

`createFromMessages()` and `createFromResponses()` do the same for the Anthropic-format and Responses APIs.

## BYOK provider credentials

Use your own provider key instead of OpenRouter's for that provider. The secret is write-only — reads return a masked `label`:

```php
use OpenRouter\ValueObjects\Byok\CreateByokKeyRequest;

$client->byok()->create(new CreateByokKeyRequest(
    provider: 'anthropic',
    key: $_ENV['ANTHROPIC_API_KEY'],
    name: 'Anthropic production',
    isFallback: true,
    allowedModels: ['anthropic/claude-sonnet-4'],
));

foreach ($client->byok()->list(provider: 'anthropic')->data as $credential) {
    echo $credential->label;   // sk-ant-...4f2a
}
```

## Observability destinations

Broadcast generation telemetry to Langfuse, Datadog, S3, a plain webhook and thirteen other sinks. They share one envelope and differ in `type` and the shape of `config`:

```php
use OpenRouter\ValueObjects\Observability\CreateObservabilityDestinationRequest;

$client->observability()->create(new CreateObservabilityDestinationRequest(
    type: 'langfuse',
    name: 'Langfuse prod',
    config: ['host' => 'https://cloud.langfuse.com'],
    samplingRate: 0.5,
    privacyMode: true,
));
```

## SCIM

Groups synchronised from your identity provider, and the mappings that place their members into workspaces. Groups are read-only here — membership is managed in the IdP:

```php
use OpenRouter\Enums\Workspaces\WorkspaceRole;

foreach ($client->scim()->listGroups()->data as $group) {
    echo $group->displayName;   // Engineering
}

$client->scim()->createGroupMapping(
    scimGroupId: 'sg_1',
    workspaceId: 'ws_1',
    role: WorkspaceRole::Member,
);

// keepMembers decides whether provisioned users stay behind
$client->scim()->deleteGroupMapping('sgm_1', keepMembers: true);
```

### Directory sync jobs

Trigger a sync of the IdP directory and poll it to completion. The job is queued asynchronously, so `createSyncJob()` returns immediately with a `queued` status:

```php
use OpenRouter\Enums\Scim\ScimSyncJobStatus;

$job = $client->scim()->createSyncJob()->data;

while (! $job->status->isTerminal()) {
    sleep(2);
    $job = $client->scim()->retrieveSyncJob($job->id)->data;
}

if ($job->status === ScimSyncJobStatus::Succeeded) {
    echo "synced {$job->syncedGroups}, deleted {$job->deletedGroups}";
} else {
    echo $job->errorMessage;   // stable message describing the failure
}
```

`status` is an open enum: a value OpenRouter adds later decodes to `ScimSyncJobStatus::Unknown` rather than throwing, and the wire value stays on `$job->rawStatus`. `Unknown` is deliberately not terminal, so the loop above keeps polling instead of exiting early.

## Analytics

`meta()` first, to discover the valid metric and dimension identifiers rather than hardcoding them:

```php
$meta = $client->analytics()->meta()->data;

$result = $client->analytics()->query([
    'metrics' => ['tokens', 'cost'],
    'dimensions' => ['model'],
    'granularity' => 'day',
    'time_range' => ['start' => '2026-08-01', 'end' => '2026-09-01'],
])->data;
```

The result shape follows the query, so both come back as raw arrays rather than a type the spec does not define.

## Datasets and benchmarks

Public, platform-wide aggregates — not your own account:

```php
$client->datasets()->appRankings(category: 'programming', limit: 10)->data;
$client->datasets()->dailyRankings(startDate: '2026-08-01', endDate: '2026-09-01')->data;
$client->datasets()->sessionCost(appSlug: 'cursor')->data;

$client->benchmarks()->list(taskType: 'coding')->data;
$client->benchmarks()->taskClassification(window: '30d')->data;
```

## Generation feedback and content

```php
use OpenRouter\Enums\Generation\FeedbackCategory;

$client->generation()->submitFeedback(
    'gen_1',
    FeedbackCategory::IncorrectResponse,
    'The answer cited a page that does not exist.',
);

// Stored prompt/completion, when logging is enabled for the account
$content = $client->generation()->content('gen_1')->data;
```

## Embeddings

```php
use OpenRouter\Enums\Embeddings\EncodingFormat;
use OpenRouter\ValueObjects\Embeddings\CreateEmbeddingsRequest;

$response = $client->embeddings()->generate(new CreateEmbeddingsRequest(
    input: ['The quick brown fox', 'jumps over the lazy dog'],
    model: 'openai/text-embedding-3-small',
    dimensions: 1536,
    encodingFormat: EncodingFormat::Float,
));

$response->data[0]->embedding;   // list<float>
$response->usage->promptTokens;
$response->usage->cost;

$models = $client->embeddings()->listModels();

foreach ($models->data as $model) {
    echo $model->id.PHP_EOL;
}
```

## Rerank

Rerank a list of documents against a search query.

```php
use OpenRouter\ValueObjects\Rerank\RerankRequest;

$response = $client->rerank()->rerank(new RerankRequest(
    model: 'cohere/rerank-v3.5',
    query: 'What is the capital of France?',
    documents: [
        'Paris is the capital of France.',
        'Berlin is the capital of Germany.',
        'Madrid is the capital of Spain.',
    ],
    topN: 3,
));

foreach ($response->results as $result) {
    echo "{$result->relevanceScore} - {$result->document->text}".PHP_EOL;
}

$response->usage->searchUnits;
$response->usage->totalTokens;
```

## API Keys

Inspect the current key, or manage API keys (list/create/retrieve/update/delete). Management operations require a management key.

```php
use OpenRouter\Enums\Keys\LimitReset;
use OpenRouter\ValueObjects\Keys\CreateKeyRequest;
use OpenRouter\ValueObjects\Keys\UpdateKeyRequest;

$current = $client->keys()->current();
$current->data->label;          // 'sk-or-v1-au7...890'
$current->data->limitRemaining; // 74.5

$keys = $client->keys()->list(includeDisabled: false, offset: 0);
foreach ($keys->data as $key) {
    echo "{$key->hash} - {$key->name} (\${$key->usage})".PHP_EOL;
}

$created = $client->keys()->create(new CreateKeyRequest(
    name: 'My New API Key',
    limit: 50.0,
    limitReset: LimitReset::Monthly,
    includeByokInLimit: true,
));
$created->key; // full API key string - returned once at creation time

$retrieved = $client->keys()->retrieve($created->data->hash);

$updated = $client->keys()->update($created->data->hash, new UpdateKeyRequest(
    disabled: true,
    limit: 100.0,
));

$client->keys()->delete($created->data->hash)->deleted; // true
```

## OAuth (PKCE)

Create an authorization code and exchange it for a user-controlled API key.

```php
use OpenRouter\Enums\Auth\CodeChallengeMethod;
use OpenRouter\ValueObjects\Auth\CreateAuthCodeRequest;
use OpenRouter\ValueObjects\Auth\ExchangeCodeRequest;

$code = $client->auth()->createAuthCode(new CreateAuthCodeRequest(
    callbackUrl: 'https://myapp.com/auth/callback',
    codeChallenge: $pkceChallenge,
    codeChallengeMethod: CodeChallengeMethod::S256,
    limit: 100.0,
    keyLabel: 'My Custom Key',
));
$code->data->id; // redirect the user with this auth code

// Back on your callback URL, exchange the code for an API key:
$exchange = $client->auth()->exchangeCode(new ExchangeCodeRequest(
    code: $_GET['code'],
    codeVerifier: $pkceVerifier,
    codeChallengeMethod: CodeChallengeMethod::S256,
));
$exchange->key;     // sk-or-v1-...
$exchange->userId;  // user_...
```

## Workload identity (token exchange)

RFC 8693 token exchange: present a JWT from an issuer your organization trusts (Settings → Workload identity) and receive a short-lived OpenRouter access token. This lets a CI job or a pod authenticate with its existing identity instead of a long-lived API key.

```php
use OpenRouter\ValueObjects\Oauth\TokenExchangeRequest;

$token = $client->oauth()->exchangeToken(new TokenExchangeRequest(
    subjectToken: $jwtFromYourIdp,
    federationPolicyId: '4b2f7d1e-8c3a-4e5f-9a6b-1c2d3e4f5a6b',
));

$token->accessToken;   // send as Authorization: Bearer to the inference API
$token->expiresIn;     // 900 — at most 15 minutes
$token->expiresAt();   // absolute Unix time, for caching
$token->isExpired(leewaySeconds: 30);
```

This call authenticates with the subject token, so the SDK deliberately omits the `Authorization` header — you can build the client without an API key when token exchange is all you need.

Verify a token locally against OpenRouter's published signing keys:

```php
$jwks = $client->oauth()->jwks();

$jwks->findKey('or-2026-09')?->alg;   // ES256
$jwks->toArray();                     // hand straight to a JWT library
```

## Organization members

List members of the authenticated organization. Requires a management key. Supports offset/limit pagination (max `limit` = 100).

```php
$members = $client->organization()->listMembers(offset: 0, limit: 50);

$members->totalCount; // 25

foreach ($members->data as $member) {
    echo "{$member->email} - {$member->role}".PHP_EOL;
}
```

### Creating a customer organization

For Connect-enabled organizations: create a customer organization owned by a managed user. Requires a management key.

```php
$result = $client->organization()->create('Acme Corp', 'owner@acme.example');

$result->created;                  // false on an idempotent replay
$result->organization->slug;       // parent-acme-corp
$result->grant->scopes;            // ['inference', 'keys_read', ...]
$result->managementKey?->key;      // plaintext — returned once, store it now
```

The plaintext management key is handed back only by the call that mints it. A repeat call for the same customer returns the existing organization with `created` false and `managementKey` null, so treat a non-null key as a one-time value. The organization is created unfunded — fund it before running inference.

## Guardrails

Manage spend-limit guardrails and assign them to API keys or organization members. All operations require a management key. List endpoints support offset/limit pagination (max `limit` = 100).

```php
use OpenRouter\Enums\Guardrails\GuardrailInterval;
use OpenRouter\ValueObjects\Guardrails\CreateGuardrailRequest;
use OpenRouter\ValueObjects\Guardrails\UpdateGuardrailRequest;

// List, create, retrieve, update, delete
$list = $client->guardrails()->list(offset: 0, limit: 50);
$list->totalCount;
foreach ($list->data as $g) {
    echo "{$g->id} - {$g->name} (\${$g->limitUsd})".PHP_EOL;
}

$created = $client->guardrails()->create(new CreateGuardrailRequest(
    name: 'Production Guardrail',
    description: 'Spend cap for prod keys',
    limitUsd: 100.0,
    resetInterval: GuardrailInterval::Monthly,
    allowedProviders: ['openai', 'anthropic'],
    enforceZdr: true,
));
$id = $created->data->id;

$client->guardrails()->retrieve($id);

$client->guardrails()->update($id, new UpdateGuardrailRequest(
    limitUsd: 150.0,
    resetInterval: GuardrailInterval::Weekly,
));

$client->guardrails()->delete($id)->deleted; // true

// Bulk assign/unassign API keys to a guardrail
$client->guardrails()->bulkAssignKeys($id, ['hash1', 'hash2']);
$client->guardrails()->listKeyAssignments($id, limit: 100);
$client->guardrails()->bulkUnassignKeys($id, ['hash1']);

// Bulk assign/unassign organization members
$client->guardrails()->bulkAssignMembers($id, ['user_abc123', 'user_def456']);
$client->guardrails()->listMemberAssignments($id);
$client->guardrails()->bulkUnassignMembers($id, ['user_abc123']);

// List every assignment across the account
$client->guardrails()->listAllKeyAssignments();
$client->guardrails()->listAllMemberAssignments();
```

## Generation metadata

Retrieve metadata for a previously-issued generation by its ID:

```php
$generation = $client->generation()->retrieve('gen-3bhGkxlo4XFrqiabUM7NDtwDzWwG');

$generation->data->model;            // 'sao10k/l3-stheno-8b'
$generation->data->totalCost;        // 0.0015
$generation->data->tokensPrompt;     // 10
$generation->data->tokensCompletion; // 25
$generation->data->providerName;     // 'Infermatic'
```

## Activity

Returns user activity data grouped by endpoint for the last 30 (completed) UTC days. Requires a management key.

```php
$activity = $client->activity()->list(
    date: '2025-08-24',
    apiKeyHash: 'abc123...',
    userId: 'user_abc123',
);

foreach ($activity->data as $row) {
    echo "{$row->date} {$row->model} \${$row->usage} ({$row->requests} reqs)".PHP_EOL;
}
```

## Credits

Returns the total credits purchased and used for the authenticated user. Requires a management key.

```php
$credits = $client->credits()->retrieve();

$credits->data->totalCredits; // 100.5
$credits->data->totalUsage;   // 25.75
```

The `$client->credits()->createCoinbaseCharge()` method maps to the deprecated `/credits/coinbase` endpoint - it always raises an `ErrorException` because the upstream API has been permanently removed. Use the OpenRouter web credits purchase flow instead.

## Providers

List all providers known to OpenRouter with their metadata (headquarters, datacenter locations, policy URLs).

```php
foreach ($client->providers()->list()->data as $provider) {
    echo "{$provider->slug} - {$provider->name} ({$provider->headquarters})".PHP_EOL;
    foreach ($provider->datacenters ?? [] as $dc) {
        echo "  dc: {$dc}".PHP_EOL;
    }
}
```

## Endpoints (ZDR preview)

Preview the impact of Zero Data Retention on the set of available endpoints.

```php
foreach ($client->endpoints()->listZdr()->data as $endpoint) {
    echo "{$endpoint->name} - {$endpoint->providerName} / {$endpoint->modelId}".PHP_EOL;
}
```

## OpenRouter-specific headers

```php
$client = OpenRouter::factory()
    ->withApiKey($_ENV['OPENROUTER_API_KEY'])
    ->withHttpReferer('https://myapp.com')              // HTTP-Referer
    ->withAppTitle('My App')                            // X-Title
    ->withAppCategories(['cli-agent', 'cloud-agent'])   // X-OpenRouter-Categories
    ->make();
```

## Custom configuration

```php
$client = OpenRouter::factory()
    ->withApiKey($apiKey)
    ->withBaseUri('https://eu.openrouter.ai/api/v1')
    ->withHttpClient($customPsr18Client)
    ->withHttpHeader('X-Custom-Header', 'value')
    ->withQueryParam('foo', 'bar')
    ->make();
```

## Error handling

All HTTP errors map to dedicated exceptions in `OpenRouter\Exceptions\Http\*` (`UnauthorizedException`, `PaymentRequiredException`, `TooManyRequestsException`, etc.) extending `ErrorException`. Streaming errors are decoded from `error:` SSE frames and thrown mid-iteration.

```php
use OpenRouter\Exceptions\ErrorException;
use OpenRouter\Exceptions\Http\TooManyRequestsException;

try {
    $client->chat()->send([...]);
} catch (TooManyRequestsException $e) {
    sleep(1);
} catch (ErrorException $e) {
    error_log("OpenRouter: {$e->getMessage()} ({$e->getStatusCode()})");
}
```

## Forward compatibility

Unknown discriminator values (new tool types, message roles, content parts, response formats, stream event types) hydrate to `Unknown*` fallbacks that preserve the raw payload - your code keeps working when OpenRouter ships new variants.

Unknown **fields** are kept too. Every value object that models an API entity exposes an `$extras` array holding the keys this SDK does not have a typed accessor for yet, and re-emits them from `toArray()`:

```php
$model = $client->models()->list()->data[0];

// Shipped by the API, not yet typed by this SDK:
$benchmarks = $model->extras['benchmarks'] ?? null;
```

Requests have the same escape hatch: `CreateChatRequest` (and friends) take an `$extras` array that is merged into the request body, and `$client->chat()->send([...])` forwards a plain array untouched. You never have to wait for an SDK release to reach a new API parameter.

### Staying in sync with the API

The OpenRouter spec this SDK targets is vendored at `openapi-openrouter.yaml`, and `api-coverage.json` records every upstream operation as either `covered` (a typed wrapper exists) or a reviewed `known_gaps` entry.

```bash
php bin/check-api-drift.php                        # fetch the live spec and diff it
php bin/check-api-drift.php openapi-openrouter.yaml # check the vendored copy offline
```

The check runs in CI on every push (against the vendored spec) and weekly against `https://openrouter.ai/openapi.yaml`, so a new upstream endpoint fails the build instead of going unnoticed.

## Changelog and upgrading

Every release is documented in [CHANGELOG.md](CHANGELOG.md), grouped into features, fixes
and BC breaks.

[UPGRADE.md](UPGRADE.md) covers the 0.x → 1.0 migration. The short version: 1.0 is
additive for code that uses the SDK, and breaking only for code that *implements* its
contract interfaces — a custom transporter or a hand-written fake client.

This project follows [Semantic Versioning](https://semver.org/). From 1.0 on, the contract
interfaces will not change before 2.0.0.

## Testing

```bash
composer install
./vendor/bin/phpunit
```

## Acknowledgements

This library is heavily inspired by [`openai-php/client`](https://github.com/openai-php/client) - its architecture, resource/factory/transporter split, and value object ergonomics shaped much of the design here. Huge thanks to its authors and contributors.

## License

MIT
