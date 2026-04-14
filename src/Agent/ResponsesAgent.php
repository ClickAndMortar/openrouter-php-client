<?php

declare(strict_types=1);

namespace OpenRouter\Agent;

use Closure;
use OpenRouter\Agent\Exceptions\MaxToolRoundsReached;
use OpenRouter\Agent\Exceptions\UnregisteredTool;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Resources\Responses;
use OpenRouter\ValueObjects\Responses\CreateResponseRequest;
use OpenRouter\ValueObjects\Responses\Input\InputFunctionCall;
use OpenRouter\ValueObjects\Responses\Input\InputFunctionCallOutput;
use OpenRouter\ValueObjects\Responses\Input\InputItem;
use OpenRouter\ValueObjects\Responses\Input\InputMessage;
use OpenRouter\ValueObjects\Responses\Tools\FunctionTool;
use Throwable;

/**
 * Fluent builder + runner for agentic `/responses` calls. Uses
 * `previous_response_id` when the server returns one (so only the new input
 * items need to be sent on follow-ups), falling back to sending the full
 * accumulated input list when it doesn't.
 */
final class ResponsesAgent
{
    /** @var list<InputItem> */
    private array $input = [];

    private ?string $instructions = null;

    /** @var array<string, AgentTool> */
    private array $tools = [];

    private ?string $model = null;

    private ?float $temperature = null;

    private ?float $topP = null;

    private ?int $maxOutputTokens = null;

    private ?int $maxToolCalls = null;

    private ?bool $parallelToolCalls = null;

    /** @var string|array<string, mixed>|null */
    private string|array|null $toolChoice = null;

    /** @var array<string, mixed> */
    private array $extraParams = [];

    /** @var int|Closure(int, AgentStep): bool */
    private int|Closure $maxToolRounds = 5;

    private bool $throwOnMaxRounds = true;

    private bool $rethrowToolExceptions = false;

    public function __construct(private readonly Responses $responses)
    {
    }

    public function model(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function instructions(string $instructions): self
    {
        $this->instructions = $instructions;
        return $this;
    }

    public function user(string $content): self
    {
        $this->input[] = InputMessage::user($content);
        return $this;
    }

    public function system(string $content): self
    {
        $this->input[] = InputMessage::system($content);
        return $this;
    }

    public function developer(string $content): self
    {
        $this->input[] = InputMessage::developer($content);
        return $this;
    }

    /**
     * @param  list<InputItem>  $items
     */
    public function input(array $items): self
    {
        foreach ($items as $item) {
            if (! $item instanceof InputItem) {
                throw new InvalidArgumentException(
                    'ResponsesAgent::input() expects a list of InputItem instances',
                );
            }
            $this->input[] = $item;
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

    public function maxOutputTokens(int $maxOutputTokens): self
    {
        $this->maxOutputTokens = $maxOutputTokens;
        return $this;
    }

    public function maxToolCalls(int $maxToolCalls): self
    {
        $this->maxToolCalls = $maxToolCalls;
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
     * @param  array<string, mixed>  $params
     */
    public function extra(array $params): self
    {
        $this->extraParams = [...$this->extraParams, ...$params];
        return $this;
    }

    /**
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

    public function run(?string $prompt = null): AgentRunResult
    {
        if ($prompt !== null) {
            $this->user($prompt);
        }

        if ($this->model === null) {
            throw new InvalidArgumentException('ResponsesAgent::run() requires ->model(...)');
        }
        if ($this->input === []) {
            throw new InvalidArgumentException(
                'ResponsesAgent::run() requires at least one input item — add one via user()/system()/input().',
            );
        }

        $steps = [];
        $turn = 0;

        /** @var list<InputItem> $pendingInput Items not yet sent when chaining with previous_response_id. */
        $pendingInput = $this->input;
        $previousResponseId = null;

        while (true) {
            $request = $this->buildRequest($pendingInput, $previousResponseId);
            $response = $this->responses->send($request);

            $toolCalls = $response->toolCalls();

            $shouldLoop = $toolCalls !== [];

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

            $nextInput = [];
            $results = [];

            // If the server stored the response, we can chain via previous_response_id
            // and only send the new function_call_output items; otherwise we must
            // echo the prior function_call items plus the outputs.
            $canChain = $response->id !== '';

            if (! $canChain) {
                // Fallback: accumulate everything locally.
                // Echo the function_call items the model emitted so the next turn sees the full history.
                foreach ($toolCalls as $call) {
                    $this->input[] = new InputFunctionCall(
                        callId: $call->callId,
                        name: $call->name,
                        arguments: $call->arguments,
                        id: $call->id,
                        status: $call->status,
                    );
                }
            }

            foreach ($toolCalls as $call) {
                $tool = $this->tools[$call->name] ?? null;
                if ($tool === null) {
                    throw UnregisteredTool::for($call->name);
                }

                $ctx = new AgentToolContext($turn, $call->callId, $call->name);
                try {
                    $result = ($tool->execute)($call->decodedArguments(), $ctx);
                } catch (Throwable $e) {
                    if ($this->rethrowToolExceptions) {
                        throw $e;
                    }
                    $result = ['error' => $e->getMessage()];
                }

                $payload = is_string($result) ? $result : (string) json_encode($result);
                $results[$call->callId] = $result;
                $output = new InputFunctionCallOutput(callId: $call->callId, output: $payload);
                if ($canChain) {
                    $nextInput[] = $output;
                } else {
                    $this->input[] = $output;
                }
            }

            $steps[] = new AgentStep($turn, $response, $toolCalls, $results);

            if ($canChain) {
                $pendingInput = $nextInput;
                $previousResponseId = $response->id;
            } else {
                $pendingInput = $this->input;
                $previousResponseId = null;
            }

            $turn++;
        }
    }

    private function loopAllowed(int $completedTurns, ?AgentStep $lastStep): bool
    {
        if ($this->maxToolRounds instanceof Closure) {
            if ($lastStep === null) {
                return true;
            }
            return (bool) ($this->maxToolRounds)($completedTurns, $lastStep);
        }

        return $completedTurns < $this->maxToolRounds;
    }

    /**
     * @param  list<InputItem>  $inputItems
     */
    private function buildRequest(array $inputItems, ?string $previousResponseId): CreateResponseRequest
    {
        $tools = [];
        foreach ($this->tools as $t) {
            $tools[] = new FunctionTool(
                name: $t->name,
                parameters: $t->parameters,
                description: $t->description,
                strict: $t->strict,
            );
        }

        /** @var string $model */
        $model = $this->model;

        return new CreateResponseRequest(
            model: $model,
            input: array_values($inputItems),
            tools: $tools === [] ? null : $tools,
            toolChoice: $this->toolChoice,
            parallelToolCalls: $this->parallelToolCalls,
            temperature: $this->temperature,
            topP: $this->topP,
            maxOutputTokens: $this->maxOutputTokens,
            instructions: $this->instructions,
            previousResponseId: $previousResponseId,
            maxToolCalls: $this->maxToolCalls,
            extras: $this->extraParams,
        );
    }
}
