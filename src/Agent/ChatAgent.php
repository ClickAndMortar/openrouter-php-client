<?php

declare(strict_types=1);

namespace OpenRouter\Agent;

use Closure;
use OpenRouter\Agent\Exceptions\MaxToolRoundsReached;
use OpenRouter\Agent\Exceptions\UnregisteredTool;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Resources\Chat;
use OpenRouter\ValueObjects\Chat\CreateChatRequest;
use OpenRouter\ValueObjects\Chat\Messages\AssistantMessage;
use OpenRouter\ValueObjects\Chat\Messages\ChatMessage;
use OpenRouter\ValueObjects\Chat\Messages\SystemMessage;
use OpenRouter\ValueObjects\Chat\Messages\ToolMessage;
use OpenRouter\ValueObjects\Chat\Messages\UserMessage;
use OpenRouter\ValueObjects\Chat\Tools\ChatFunctionTool;
use OpenRouter\ValueObjects\Chat\Tools\ChatToolCallRequest;
use Throwable;

/**
 * Fluent builder + runner for agentic `/chat/completions` calls. Registers
 * tools with executors, runs the multi-turn loop (model → tool call → result
 * → model → …) automatically, and returns an {@see AgentRunResult}.
 *
 * Mirrors the ergonomics of OpenRouter's TypeScript `callModel` SDK but stays
 * synchronous and dependency-free. Reuses the existing {@see CreateChatRequest}
 * VOs — nothing here bypasses the typed request/response layer.
 */
final class ChatAgent
{
    /** @var list<ChatMessage> */
    private array $messages = [];

    /** @var array<string, AgentTool> */
    private array $tools = [];

    private ?string $model = null;

    private ?float $temperature = null;

    private ?float $topP = null;

    private ?int $maxTokens = null;

    private ?bool $parallelToolCalls = null;

    /** @var string|array<string, mixed>|null */
    private string|array|null $toolChoice = null;

    /** @var array<string, mixed>|null */
    private ?array $responseFormat = null;

    /** @var array<string, mixed> */
    private array $extraParams = [];

    /** @var int|Closure(int, AgentStep): bool */
    private int|Closure $maxToolRounds = 5;

    private bool $throwOnMaxRounds = true;

    private bool $rethrowToolExceptions = false;

    public function __construct(private readonly Chat $chat)
    {
    }

    public function model(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function system(string $content): self
    {
        $this->messages[] = new SystemMessage($content);
        return $this;
    }

    /**
     * @param  string|list<\OpenRouter\ValueObjects\Chat\Content\ChatContentPart>  $content
     */
    public function user(string|array $content): self
    {
        $this->messages[] = new UserMessage($content);
        return $this;
    }

    public function assistant(?string $content = null): self
    {
        $this->messages[] = new AssistantMessage($content);
        return $this;
    }

    /**
     * Seed the agent with a pre-built transcript (replays from a previous run,
     * persisted conversation, etc.). Appends to any messages already added.
     *
     * @param  list<ChatMessage>  $messages
     */
    public function messages(array $messages): self
    {
        foreach ($messages as $m) {
            if (! $m instanceof ChatMessage) {
                throw new InvalidArgumentException(
                    'ChatAgent::messages() expects a list of ChatMessage instances',
                );
            }
            $this->messages[] = $m;
        }
        return $this;
    }

    public function tool(AgentTool|AgentToolDefinition $tool): self
    {
        $normalised = $tool instanceof AgentToolDefinition ? AgentTool::fromDefinition($tool) : $tool;
        $this->tools[$normalised->name] = $normalised;
        return $this;
    }

    /**
     * @param  list<AgentTool|AgentToolDefinition>  $tools
     */
    public function tools(array $tools): self
    {
        foreach ($tools as $t) {
            $this->tool($t);
        }
        return $this;
    }

    public function temperature(float $temperature): self
    {
        $this->temperature = $temperature;
        return $this;
    }

    public function topP(float $topP): self
    {
        $this->topP = $topP;
        return $this;
    }

    public function maxTokens(int $maxTokens): self
    {
        $this->maxTokens = $maxTokens;
        return $this;
    }

    public function parallelToolCalls(bool $enabled): self
    {
        $this->parallelToolCalls = $enabled;
        return $this;
    }

    /**
     * @param  string|array<string, mixed>  $choice
     */
    public function toolChoice(string|array $choice): self
    {
        $this->toolChoice = $choice;
        return $this;
    }

    /**
     * @param  array<string, mixed>  $format
     */
    public function responseFormat(array $format): self
    {
        $this->responseFormat = $format;
        return $this;
    }

    /**
     * Merge extra request parameters (seed, stop, provider, etc.) passed
     * straight to `CreateChatRequest`'s extras bag.
     *
     * @param  array<string, mixed>  $params
     */
    public function extra(array $params): self
    {
        $this->extraParams = [...$this->extraParams, ...$params];
        return $this;
    }

    /**
     * `int` cap or a predicate `fn(int $turn, AgentStep $lastStep): bool`
     * returning true while the loop should continue. `0` disables auto-execution
     * entirely: the runner calls the model once and returns tool calls verbatim.
     *
     * @param  int|Closure(int, AgentStep): bool  $limit
     */
    public function maxToolRounds(int|Closure $limit): self
    {
        if (is_int($limit) && $limit < 0) {
            throw new InvalidArgumentException('maxToolRounds must be >= 0');
        }
        $this->maxToolRounds = $limit;
        return $this;
    }

    public function throwOnMaxRounds(bool $throw): self
    {
        $this->throwOnMaxRounds = $throw;
        return $this;
    }

    public function rethrowToolExceptions(bool $rethrow): self
    {
        $this->rethrowToolExceptions = $rethrow;
        return $this;
    }

    /**
     * Optional final prompt; equivalent to `->user($prompt)->run()`.
     */
    public function run(?string $prompt = null): AgentRunResult
    {
        if ($prompt !== null) {
            $this->user($prompt);
        }

        if ($this->messages === []) {
            throw new InvalidArgumentException(
                'ChatAgent::run() requires at least one message — add one via system()/user()/messages().',
            );
        }
        if ($this->model === null) {
            throw new InvalidArgumentException('ChatAgent::run() requires ->model(...)');
        }

        $steps = [];
        $turn = 0;

        while (true) {
            $request = $this->buildRequest();
            $response = $this->chat->send($request);

            $toolCalls = $response->toolCalls();
            $finishReason = $response->finishReason();

            $shouldLoop = $toolCalls !== [] && $finishReason === 'tool_calls';

            if (! $shouldLoop) {
                $steps[] = new AgentStep($turn, $response, $toolCalls, []);
                return new AgentRunResult($response, $steps, false);
            }

            if (! $this->loopAllowed($turn, $steps === [] ? null : $steps[array_key_last($steps)])) {
                $steps[] = new AgentStep($turn, $response, $toolCalls, []);
                if ($this->throwOnMaxRounds) {
                    throw new MaxToolRoundsReached(sprintf(
                        'Max tool rounds reached after %d turn(s) with %d outstanding tool call(s).',
                        $turn + 1,
                        count($toolCalls),
                    ));
                }
                return new AgentRunResult($response, $steps, true);
            }

            // Echo the assistant tool-call message back so the model can see it on the next turn.
            $this->messages[] = new AssistantMessage(
                content: null,
                toolCalls: array_map(
                    static fn ($tc) => new ChatToolCallRequest(
                        id: $tc->id,
                        functionName: $tc->functionName,
                        functionArguments: $tc->functionArguments,
                    ),
                    $toolCalls,
                ),
            );

            $results = [];
            foreach ($toolCalls as $call) {
                $tool = $this->tools[$call->functionName] ?? null;
                if ($tool === null) {
                    throw UnregisteredTool::for($call->functionName);
                }

                $ctx = new AgentToolContext($turn, $call->id, $call->functionName);
                try {
                    $result = ($tool->execute)($call->arguments(), $ctx);
                } catch (Throwable $e) {
                    if ($this->rethrowToolExceptions) {
                        throw $e;
                    }
                    $result = ['error' => $e->getMessage()];
                }

                $payload = is_string($result) ? $result : (string) json_encode($result);
                $results[$call->id] = $result;
                $this->messages[] = new ToolMessage(content: $payload, toolCallId: $call->id);
            }

            $steps[] = new AgentStep($turn, $response, $toolCalls, $results);
            $turn++;
        }
    }

    private function loopAllowed(int $completedTurns, ?AgentStep $lastStep): bool
    {
        if ($this->maxToolRounds instanceof Closure) {
            if ($lastStep === null) {
                // No step yet — first round is always allowed when using a predicate.
                return true;
            }
            return (bool) ($this->maxToolRounds)($completedTurns, $lastStep);
        }

        return $completedTurns < $this->maxToolRounds;
    }

    private function buildRequest(): CreateChatRequest
    {
        $tools = [];
        foreach ($this->tools as $t) {
            $tools[] = new ChatFunctionTool(
                name: $t->name,
                parameters: $t->parameters,
                description: $t->description,
                strict: $t->strict,
            );
        }

        return new CreateChatRequest(
            messages: array_values($this->messages),
            model: $this->model,
            temperature: $this->temperature,
            topP: $this->topP,
            maxTokens: $this->maxTokens,
            tools: $tools === [] ? null : $tools,
            toolChoice: $this->toolChoice,
            parallelToolCalls: $this->parallelToolCalls,
            responseFormat: $this->responseFormat,
            extras: $this->extraParams,
        );
    }
}
