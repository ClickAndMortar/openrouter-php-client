# Changelog

All notable changes to this project are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this
project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). Entries
are grouped as **Features** (`feat`), **Fixes** (`fix`), **BC breaks** and
**Documentation** (`docs`).

Upgrading from a 0.x release? See [UPGRADE.md](UPGRADE.md).

## [Unreleased]

Nothing yet.

## [1.0.1] - 2026-09-03

No code changes — 1.0.1 exists so the guides ship inside the released package.

### Documentation

- Added this changelog, covering every release back to 0.1.0.
- Added [UPGRADE.md](UPGRADE.md) for the 0.x → 1.0 migration, and linked both from the
  README.

## [1.0.0] - 2026-09-03

Full coverage of the OpenRouter API: all 101 operations in the OpenAPI spec now have a
typed wrapper, up from 38. The public API is declared stable — the contract interfaces
will not change again before 2.0.0.

### Features

- **Files and container files.** `$client->files()` uploads, lists, retrieves, downloads
  and deletes stored files; `$client->containers()` reads code-interpreter output and
  promotes a file into durable workspace documents. The three negotiated payload shapes
  (`openrouter`, `openai`, `anthropic`) are modelled by one `StoredFile`, with
  `sizeInBytes()` reading the size without branching on `_shape`.
- **Media generation.** `$client->images()` generates images buffered or streamed (three
  typed SSE frames), `$client->videos()` submits, polls and downloads asynchronous video
  jobs, and `$client->audio()` synthesises speech and transcribes audio from either
  inline JSON or a multipart upload.
- **Workspaces, presets and BYOK.** `$client->workspaces()` covers CRUD, per-interval
  budgets and bulk member changes; `$client->presets()` covers preset and version
  listings plus the three preset-scoped inference endpoints; `$client->byok()` manages
  provider credentials whose secret is write-only.
- **Administration and reporting.** `$client->scim()`, `$client->observability()`,
  `$client->analytics()`, `$client->datasets()` and `$client->benchmarks()`, plus
  `$client->models()->retrieve()` and `$client->generation()->content()` /
  `submitFeedback()`.
- **Transport primitives.** `Payload::upload()` builds an RFC 7578 `multipart/form-data`
  body from an `UploadedFile` part plus scalars and repeated `name[]` fields;
  `TransporterContract::requestContent()` returns a `BinaryResponse` carrying raw bytes,
  the upstream `Content-Type` and `saveTo()`. `Payload::put()` was added for the budget
  upsert. Both are reachable from `$client->transporter()`, so the escape hatch now
  covers every endpoint.
- **Chat request parameters** the API had documented but the SDK never typed: `top_k`,
  `min_p`, `top_a`, `repetition_penalty`, `reasoning_effort`, `prediction`,
  `prompt_cache_key`, `prompt_cache_options` and `stop_server_tools_when`.
- **`openrouter:*` server tools** — 25 types across `/responses`, `/chat/completions` and
  `/messages`, modelled by one value object per endpoint since they share a
  `{type, parameters}` envelope. Adds `NamespaceTool`, which has its own shape.
- **Plugins** `auto-beta-router`, `pareto-router`, `fusion` and `web-fetch`, plus the
  `excluded_models`, `cost_tier`, `cost_quality_tradeoff` and `pin_model` options
  `AutoRouterPlugin` was missing.
- **19 typed `/responses` stream events** covering apply-patch diffs, the code
  interpreter, custom tool call input, the fusion panel and the debug channel. Unknown
  frames still fall back to the base class.
- **Model listing filters.** `GET /models` grew from 3 query parameters to 29; the common
  ones are named arguments and the rest pass through `$filters`. `/models/user` and
  `/embeddings/models` paginate the same way.
- **Enum values** the API added: `ServiceTier::Fast`, `ReasoningEffort::Max`,
  `MessagesStopReason::ModelContextWindowExceeded`, `ToolCallStatus::Interpreting`. Adds
  `WorkspaceRole`, `BudgetInterval` and `FeedbackCategory`.
- **Drift detection.** The OpenAPI spec is now committed, `api-coverage.json` records
  every upstream operation, and `bin/check-api-drift.php` fails CI when a new one appears
  — on every push against the committed spec, and weekly against the live one.

### Fixes

- Response value objects no longer discard fields the SDK does not recognise. Nineteen
  entity classes gained an `$extras` passthrough that also round-trips through
  `toArray()`, so new API fields reach callers before the SDK types them. `GenerationData`
  had been dropping 7 fields and `ListResponseModel` 4.
- `ListResponse` no longer discards the pagination metadata `GET /models` returns. The
  `total_count` and `links.next` fields are exposed as `$totalCount` and `$nextPage`;
  without them the endpoint could not be paged at all.

### BC breaks

Additive for code that *uses* the SDK; breaking only for code that *implements* its
interfaces or calls the affected constructors positionally. See
[UPGRADE.md](UPGRADE.md) for the migration.

- `TransporterContract` gained `requestContent()`.
- `ClientContract` gained 13 resource accessors: `analytics()`, `audio()`,
  `benchmarks()`, `byok()`, `containers()`, `datasets()`, `files()`, `images()`,
  `observability()`, `presets()`, `scim()`, `videos()`, `workspaces()`.
- `ModelsContract::list()` gained a fourth `array $filters = []` parameter;
  `listForUser()` went from no parameters to `(?int $limit, ?int $offset, ?string $outputModalities)`;
  `retrieve()` was added.
- `EmbeddingsContract::listModels()` gained `(?int $limit, ?int $offset)`.
- `GenerationContract` gained `content()` and `submitFeedback()`.
- `Payload::delete()` gained a fourth `array $query = []` parameter.
- `CreateChatRequest`: nine parameters were inserted before `$extras`, moving it from
  the 34th to the 43rd position. Named arguments are unaffected.

## [0.3.0] - 2026-04-14

### Features

- Agentic helpers: `$client->chat()->agent()` and `$client->responses()->agent()` run the
  model → tool call → tool result loop for you, up to `maxToolRounds`. Tools are declared
  as plain PHP closures via `AgentTool::define()` or as classes, and the run returns an
  `AgentRunResult` with the final answer and the intermediate `AgentStep`s.

## [0.2.0] - 2026-04-14

### Features

- Twelve further resources: `activity()`, `auth()`, `credits()`, `embeddings()`,
  `endpoints()`, `generation()`, `guardrails()`, `keys()`, `messages()`,
  `organization()`, `providers()` and `rerank()`.

## [0.1.0] - 2026-04-13

### Features

- Initial release: `chat()`, `models()` and `responses()`, with typed request and
  response value objects, SSE streaming and PSR-18 HTTP transport.

[Unreleased]: https://github.com/ClickAndMortar/openrouter-php-client/compare/v1.0.1...HEAD
[1.0.1]: https://github.com/ClickAndMortar/openrouter-php-client/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/ClickAndMortar/openrouter-php-client/compare/v0.3.0...v1.0.0
[0.3.0]: https://github.com/ClickAndMortar/openrouter-php-client/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/ClickAndMortar/openrouter-php-client/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/ClickAndMortar/openrouter-php-client/releases/tag/v0.1.0
