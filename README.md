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

Status of every endpoint in the OpenRouter OpenAPI spec:

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
| `/generation`                                 | GET                  |   ✅    | `$client->generation()->retrieve($id)`                  |
| `/activity`                                   | GET                  |   ✅    | `$client->activity()->list(...)`                        |
| `/credits`                                    | GET                  |   ✅    | `$client->credits()->retrieve()`                        |
| `/credits/coinbase`                           | POST                 |   ⚠️    | `$client->credits()->createCoinbaseCharge()` (deprecated - returns HTTP 410) |
| `/key`                                        | GET                  |   ✅    | `$client->keys()->current()`                            |
| `/keys`                                       | GET / POST           |   ✅    | `$client->keys()->list(...)` / `create(...)`            |
| `/keys/{hash}`                                | GET / PATCH / DELETE |   ✅    | `$client->keys()->retrieve($hash)` / `update(...)` / `delete($hash)` |
| `/auth/keys`                                  | POST                 |   ✅    | `$client->auth()->exchangeCode(...)`                    |
| `/auth/keys/code`                             | POST                 |   ✅    | `$client->auth()->createAuthCode(...)`                  |
| `/providers`                                  | GET                  |   ✅    | `$client->providers()->list()`                          |
| `/endpoints/zdr`                              | GET                  |   ✅    | `$client->endpoints()->listZdr()`                       |
| `/organization/members`                       | GET                  |   ✅    | `$client->organization()->listMembers(...)`             |
| `/guardrails`                                 | GET / POST           |   ✅    | `$client->guardrails()->list(...)` / `create(...)`      |
| `/guardrails/{id}`                            | GET / PATCH / DELETE |   ✅    | `$client->guardrails()->retrieve($id)` / `update(...)` / `delete($id)` |
| `/guardrails/{id}/assignments/keys`           | GET / POST           |   ✅    | `$client->guardrails()->listKeyAssignments($id, ...)` / `bulkAssignKeys($id, $hashes)` |
| `/guardrails/{id}/assignments/keys/remove`    | POST                 |   ✅    | `$client->guardrails()->bulkUnassignKeys($id, $hashes)` |
| `/guardrails/{id}/assignments/members`        | GET / POST           |   ✅    | `$client->guardrails()->listMemberAssignments($id, ...)` / `bulkAssignMembers($id, $userIds)` |
| `/guardrails/{id}/assignments/members/remove` | POST                 |   ✅    | `$client->guardrails()->bulkUnassignMembers($id, $userIds)` |
| `/guardrails/assignments/keys`                | GET                  |   ✅    | `$client->guardrails()->listAllKeyAssignments(...)`     |
| `/guardrails/assignments/members`             | GET                  |   ✅    | `$client->guardrails()->listAllMemberAssignments(...)`  |

Unsupported endpoints can still be reached through `$client->transporter()` - build a `Payload` and dispatch it manually. PRs adding typed wrappers are welcome.

```php
use OpenRouter\ValueObjects\Transporter\Payload;

$response = $client->transporter()->requestObject(
    Payload::list('organization/members'),
);

$members = $response->data();
```

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

## Organization members

List members of the authenticated organization. Requires a management key. Supports offset/limit pagination (max `limit` = 100).

```php
$members = $client->organization()->listMembers(offset: 0, limit: 50);

$members->totalCount; // 25

foreach ($members->data as $member) {
    echo "{$member->email} - {$member->role}".PHP_EOL;
}
```

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

## Testing

```bash
composer install
./vendor/bin/phpunit
```

## Acknowledgements

This library is heavily inspired by [`openai-php/client`](https://github.com/openai-php/client) - its architecture, resource/factory/transporter split, and value object ergonomics shaped much of the design here. Huge thanks to its authors and contributors.

## License

MIT
