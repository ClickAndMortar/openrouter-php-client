# Upgrading from 0.x to 1.0

**Most upgrades need no code changes.** 1.0 is almost entirely additive: it wraps the
63 API operations the SDK did not cover, and everything that worked in 0.3 still works.

```bash
composer require clickandmortar/openrouter-php-client:^1.0
```

Whether anything below applies to you comes down to one question:

| If your code…                                                    | You need to           |
|------------------------------------------------------------------|-----------------------|
| calls `$client->chat()`, `->models()`, … and reads the results     | change nothing        |
| **implements** `ClientContract`, `TransporterContract` or a `*Contract` | [add the new methods](#1-contract-interfaces-gained-methods) |
| constructs `CreateChatRequest` with **positional** arguments       | [switch to named arguments](#2-createchatrequest-parameter-order) |
| compares `toArray()` output against a fixed array                 | [read the note on `$extras`](#3-toarray-now-re-emits-unknown-fields) |
| builds `Payload` objects by hand                                  | nothing, but see [new factories](#4-payload-gained-factories) |

If none of those match, you are done.

---

## 1. Contract interfaces gained methods

This is the only change that breaks compilation, and it affects implementors only —
typically a test double, a decorator, or a fake client. Using the SDK's own classes is
unaffected.

### `TransporterContract`

One method was added, for endpoints that return bytes rather than JSON:

```php
public function requestContent(Payload $payload): BinaryResponse;
```

If you have a custom transporter, implement it. A minimal version that refuses binary
bodies is fine when you never call the audio, video or file-download endpoints:

```php
public function requestContent(Payload $payload): BinaryResponse
{
    throw new \LogicException('Binary responses are not supported by this transporter.');
}
```

### `ClientContract`

Thirteen accessors were added: `analytics()`, `audio()`, `benchmarks()`, `byok()`,
`containers()`, `datasets()`, `files()`, `images()`, `observability()`, `presets()`,
`scim()`, `videos()` and `workspaces()`.

If you implement `ClientContract` to build a fake, consider decorating the real
`OpenRouter\Client` instead — it will keep absorbing new resources for you:

```php
final class RecordingClient implements ClientContract
{
    public function __construct(private readonly ClientContract $inner) {}

    public function chat(): ChatContract
    {
        // …record, then delegate…
        return $this->inner->chat();
    }

    // Delegate the rest to $this->inner.
}
```

### Resource contracts

| Interface              | Change                                                                                             |
|------------------------|----------------------------------------------------------------------------------------------------|
| `ModelsContract`       | `list()` gained a 4th `array $filters = []`; `listForUser()` went from `()` to `(?int $limit, ?int $offset, ?string $outputModalities)`; `retrieve()` added |
| `EmbeddingsContract`   | `listModels()` gained `(?int $limit, ?int $offset)`                                                 |
| `GenerationContract`   | `content()` and `submitFeedback()` added                                                            |

Callers are unaffected — every added parameter is optional.

## 2. `CreateChatRequest` parameter order

Nine parameters were added before `$extras`, moving it from the 34th position to the
43rd. This only matters if you passed `$extras` **positionally**, which means having
written out 33 preceding arguments:

```php
// Before — still fine, and always was the intended usage
new CreateChatRequest(
    model: 'openai/gpt-4o',
    messages: [$message],
    extras: ['some_new_field' => true],
);
```

Every other value object in the SDK follows the same rule: pass arguments by name.

## 3. `toArray()` now re-emits unknown fields

Nineteen response value objects gained an `$extras` passthrough, so fields the SDK does
not recognise survive both the parse and `toArray()`. Previously they were dropped.

This is a fix — `GenerationData` had been discarding 7 fields the API sends — but it
does change `toArray()` output if you assert against a fixed array in tests:

```php
$model = $client->models()->list()->data[0];

$model->extras['benchmarks'];   // reachable before the SDK types it
$model->toArray();              // now includes 'benchmarks' too
```

If a test compares the whole array, either add the extra keys or assert on the fields
you care about.

## 4. `Payload` gained factories

`Payload::delete()` took a fourth `array $query = []` parameter — additive, so existing
calls are unchanged. Two factories are new:

```php
use OpenRouter\ValueObjects\Transporter\Payload;
use OpenRouter\ValueObjects\Transporter\UploadedFile;

Payload::upload('some/endpoint', ['file' => UploadedFile::fromPath('/path/to.bin')]);
Payload::put('some/endpoint', ['limit_usd' => 250.0]);
```

---

## Worth adopting

Nothing here is required, but 1.0 removes some workarounds.

**Endpoints you previously reached through `$client->transporter()`** now have typed
wrappers. If you hand-rolled a `Payload` for files, images, audio, video, workspaces,
presets, BYOK, SCIM, observability, analytics or datasets, you can drop it:

```php
// Before
$response = $client->transporter()->requestObject(Payload::list('presets'));
$presets = $response->data()['data'];

// After
$presets = $client->presets()->list()->data;   // list<Preset>
```

**Model pagination** is now reachable. `GET /models` returns hundreds of models; before
1.0 the SDK exposed no way to page them:

```php
$page = $client->models()->list(filters: ['limit' => 50, 'sort' => 'newest']);

$page->totalCount;   // total across all pages
$page->nextPage;     // URL of the next page, or null
```

**Chat parameters** that previously needed the `$extras` escape hatch are typed:

```php
// Before
new CreateChatRequest(model: 'openai/gpt-4o', messages: [$m], extras: ['top_k' => 40]);

// After
new CreateChatRequest(model: 'openai/gpt-4o', messages: [$m], topK: 40);
```

**Binary downloads** no longer need a bespoke HTTP client:

```php
$client->audio()->speech($request)->saveTo('hello.mp3');
$client->files()->download($fileId)->saveTo('report.pdf');
```

## Getting help

If something is not covered here, open an issue at
<https://github.com/ClickAndMortar/openrouter-php-client/issues> with the 0.x code that
stopped working — the escape hatches described in the README's
[Forward compatibility](README.md#forward-compatibility) section usually provide an
interim answer.
