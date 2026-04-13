# OpenRouter PHP Client

A PHP SDK for [OpenRouter](https://openrouter.ai) with typed request/response value objects, SSE streaming, and PSR-18 HTTP transport. The API mirrors the ergonomics of [`openai-php/client`](https://github.com/openai-php/client).

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
```

## Endpoint coverage

Status of every endpoint in the OpenRouter OpenAPI spec:

| Endpoint                                      | Method               | Status | SDK call                                                |
|-----------------------------------------------|----------------------|:------:|---------------------------------------------------------|
| `/chat/completions`                           | POST                 |   ✅    | `$client->chat()->send(...)` / `sendStreamed(...)`      |
| `/responses`                                  | POST                 |   ✅    | `$client->responses()->send(...)` / `sendStreamed(...)` |
| `/models/user`                                | GET                  |   ✅    | `$client->models()->listForUser()`                      |
| `/models`                                     | GET                  |   ❌    | -                                                       |
| `/models/count`                               | GET                  |   ❌    | -                                                       |
| `/models/{author}/{slug}/endpoints`           | GET                  |   ❌    | -                                                       |
| `/messages`                                   | POST                 |   ❌    | -                                                       |
| `/embeddings`                                 | POST                 |   ❌    | -                                                       |
| `/embeddings/models`                          | GET                  |   ❌    | -                                                       |
| `/rerank`                                     | POST                 |   ❌    | -                                                       |
| `/generation`                                 | GET                  |   ❌    | -                                                       |
| `/activity`                                   | GET                  |   ❌    | -                                                       |
| `/credits`                                    | GET                  |   ❌    | -                                                       |
| `/credits/coinbase`                           | POST                 |   ❌    | -                                                       |
| `/key`                                        | GET                  |   ❌    | -                                                       |
| `/keys`                                       | GET / POST           |   ❌    | -                                                       |
| `/keys/{hash}`                                | GET / PATCH / DELETE |   ❌    | -                                                       |
| `/auth/keys`                                  | POST                 |   ❌    | -                                                       |
| `/auth/keys/code`                             | POST                 |   ❌    | -                                                       |
| `/providers`                                  | GET                  |   ❌    | -                                                       |
| `/endpoints/zdr`                              | GET                  |   ❌    | -                                                       |
| `/organization/members`                       | GET                  |   ❌    | -                                                       |
| `/guardrails`                                 | GET / POST           |   ❌    | -                                                       |
| `/guardrails/{id}`                            | GET / PATCH / DELETE |   ❌    | -                                                       |
| `/guardrails/{id}/assignments/keys`           | GET / POST           |   ❌    | -                                                       |
| `/guardrails/{id}/assignments/keys/remove`    | POST                 |   ❌    | -                                                       |
| `/guardrails/{id}/assignments/members`        | GET / POST           |   ❌    | -                                                       |
| `/guardrails/{id}/assignments/members/remove` | POST                 |   ❌    | -                                                       |
| `/guardrails/assignments/keys`                | GET                  |   ❌    | -                                                       |
| `/guardrails/assignments/members`             | GET                  |   ❌    | -                                                       |

Unsupported endpoints can still be reached through `$client->transporter()` - build a `Payload` and dispatch it manually. PRs adding typed wrappers are welcome.

```php
use OpenRouter\ValueObjects\Transporter\Payload;

$response = $client->transporter()->requestObject(
    Payload::list('credits'),
);

$credits = $response->data();
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
