<?php

declare(strict_types=1);

namespace OpenRouter\Contracts;

use OpenRouter\Contracts\Resources\ActivityContract;
use OpenRouter\Contracts\Resources\AnalyticsContract;
use OpenRouter\Contracts\Resources\AudioContract;
use OpenRouter\Contracts\Resources\AuthContract;
use OpenRouter\Contracts\Resources\BenchmarksContract;
use OpenRouter\Contracts\Resources\ByokContract;
use OpenRouter\Contracts\Resources\ChatContract;
use OpenRouter\Contracts\Resources\ContainersContract;
use OpenRouter\Contracts\Resources\CreditsContract;
use OpenRouter\Contracts\Resources\DatasetsContract;
use OpenRouter\Contracts\Resources\EmbeddingsContract;
use OpenRouter\Contracts\Resources\EndpointsContract;
use OpenRouter\Contracts\Resources\FilesContract;
use OpenRouter\Contracts\Resources\GenerationContract;
use OpenRouter\Contracts\Resources\GuardrailsContract;
use OpenRouter\Contracts\Resources\ImagesContract;
use OpenRouter\Contracts\Resources\KeysContract;
use OpenRouter\Contracts\Resources\MessagesContract;
use OpenRouter\Contracts\Resources\ModelsContract;
use OpenRouter\Contracts\Resources\OauthContract;
use OpenRouter\Contracts\Resources\ObservabilityContract;
use OpenRouter\Contracts\Resources\OrganizationContract;
use OpenRouter\Contracts\Resources\PresetsContract;
use OpenRouter\Contracts\Resources\ProvidersContract;
use OpenRouter\Contracts\Resources\RerankContract;
use OpenRouter\Contracts\Resources\ScimContract;
use OpenRouter\Contracts\Resources\ResponsesContract;
use OpenRouter\Contracts\Resources\VideosContract;
use OpenRouter\Contracts\Resources\WorkspacesContract;

interface ClientContract
{
    /**
     * Escape hatch for endpoints not yet covered by a typed resource: build a
     * {@see \OpenRouter\ValueObjects\Transporter\Payload} and dispatch it via
     * {@see TransporterContract::requestObject()} or {@see TransporterContract::requestStream()}.
     */
    /**
     * Speech synthesis and transcription.
     */
    public function audio(): AudioContract;

    /**
     * Image generation, streamed or buffered, plus the image model catalogue.
     */
    public function images(): ImagesContract;

    /**
     * Asynchronous video generation: submit, poll, download.
     */
    public function videos(): VideosContract;

    /**
     * Usage analytics: available metrics and dimensions, and ad-hoc queries.
     */
    public function analytics(): AnalyticsContract;

    /**
     * Published benchmark results and task-classification market share.
     */
    public function benchmarks(): BenchmarksContract;

    /**
     * OpenRouter's public, platform-wide usage datasets.
     */
    public function datasets(): DatasetsContract;

    /**
     * Bring-your-own-key provider credentials.
     */
    public function byok(): ByokContract;

    /**
     * Saved inference configurations, addressed by slug.
     */
    public function presets(): PresetsContract;

    /**
     * Destinations OpenRouter broadcasts generation telemetry to.
     */
    public function observability(): ObservabilityContract;

    /**
     * SCIM groups synchronised from your IdP, and their workspace mappings.
     */
    public function scim(): ScimContract;

    /**
     * Workload-identity token exchange and the token signing keys.
     */
    public function oauth(): OauthContract;

    /**
     * Workspaces: scoped containers for keys, guardrails, budgets and members.
     */
    public function workspaces(): WorkspacesContract;

    public function transporter(): TransporterContract;


    /**
     * Create and manage OpenResponses-style generations.
     *
     * @see https://openrouter.ai/docs/api-reference/responses
     */
    public function responses(): ResponsesContract;

    /**
     * Create chat completions using the OpenAI-compatible
     * `/chat/completions` endpoint.
     *
     * @see https://openrouter.ai/docs/api-reference/chat-completion
     */
    public function chat(): ChatContract;

    /**
     * Create messages using the Anthropic-compatible `/messages` endpoint.
     *
     * @see https://openrouter.ai/docs/api-reference/create-messages
     */
    public function messages(): MessagesContract;

    /**
     * List and inspect available models.
     *
     * @see https://openrouter.ai/docs/api-reference/models
     */
    public function models(): ModelsContract;

    /**
     * Generate embedding vectors and list embedding-capable models via the
     * `/embeddings` and `/embeddings/models` endpoints.
     *
     * @see https://openrouter.ai/docs/api-reference/embeddings
     */
    public function embeddings(): EmbeddingsContract;

    /**
     * Retrieve metadata for a previously-issued generation via `/generation`.
     *
     * @see https://openrouter.ai/docs/api-reference/get-a-generation
     */
    /**
     * Files produced inside code-interpreter containers.
     */
    public function containers(): ContainersContract;

    /**
     * Stored files: upload, list, retrieve metadata, download and delete.
     */
    public function files(): FilesContract;

    public function generation(): GenerationContract;

    /**
     * Fetch user activity grouped by endpoint via `/activity`. Requires a management key.
     *
     * @see https://openrouter.ai/docs/api-reference/get-user-activity
     */
    public function activity(): ActivityContract;

    /**
     * Fetch remaining credits and access the deprecated Coinbase charge endpoint
     * via `/credits` and `/credits/coinbase`.
     *
     * @see https://openrouter.ai/docs/api-reference/get-credits
     */
    public function credits(): CreditsContract;

    /**
     * List all providers known to OpenRouter via `/providers`.
     *
     * @see https://openrouter.ai/docs/api-reference/list-providers
     */
    public function providers(): ProvidersContract;

    /**
     * Inspect the set of endpoints exposed by OpenRouter, e.g. ZDR previews via `/endpoints/zdr`.
     *
     * @see https://openrouter.ai/docs/api-reference/list-endpoints-zdr
     */
    public function endpoints(): EndpointsContract;

    /**
     * Submit document rerank requests via the `/rerank` endpoint.
     *
     * @see https://openrouter.ai/docs/api-reference/rerank
     */
    public function rerank(): RerankContract;

    /**
     * Manage API keys via the `/key`, `/keys`, and `/keys/{hash}` endpoints.
     * List/create/retrieve/update/delete operations require a management key.
     *
     * @see https://openrouter.ai/docs/api-reference/api-keys
     */
    public function keys(): KeysContract;

    /**
     * Manage spend-limit guardrails and their API-key/member assignments via
     * the `/guardrails*` endpoints. Requires a management key.
     *
     * @see https://openrouter.ai/docs/api-reference/guardrails
     */
    public function guardrails(): GuardrailsContract;

    /**
     * PKCE OAuth flow via the `/auth/keys` and `/auth/keys/code` endpoints.
     *
     * @see https://openrouter.ai/docs/api-reference/oauth
     */
    public function auth(): AuthContract;

    /**
     * List members of the authenticated organization via `/organization/members`.
     * Requires a management key.
     *
     * @see https://openrouter.ai/docs/api-reference/list-organization-members
     */
    public function organization(): OrganizationContract;
}
