<?php

namespace Aspect\Lib\PhpUnit\Faker;

use Aspect\Lib\PhpUnit\Faker;
use Aspect\Lib\PhpUnit\Faker\Dto\AddAgentParametersDto;
use Aspect\Lib\PhpUnit\Faker\Exception\DisallowedFakerException;
use Aspect\Lib\PhpUnit\Faker\Exception\ExpectedFakerException;
use Aspect\Lib\Struct\Singleton;
use Mockery\MockInterface;

class AgentFaker
{
    use Singleton;

    /**
     * @var AgentFakerRule[] $rules
     */
    private array $rules = [];
    private int $agentId = 1;

    private MockInterface $mockery;

    private array $disallowed = [];

    public function make(): ?callable
    {
        $this->mockery = \Mockery::mock("alias:".\CAgent::class);
        $mockHandler = function($name, $module = "", $period = "N", $interval = 86400, $datecheck = "", $active = "Y", $next_exec = "", $sort = 100, $user_id = false, $existError = true) {

            if ($rule = $this->rules[$name]) {
                $rule->call(AddAgentParametersDto::fromArray([
                    'name' => $name,
                    'module' => $module,
                    'period' => $period,
                    'interval' => $interval,
                    'datecheck' => $datecheck,
                    'active' => $active,
                    'next_exec' => $next_exec,
                    'sort' => $sort,
                    'user_id' => $user_id,
                    'existError' => $existError
                ]));
            } else {
                $this->disallowed[] = $name;
            }

            return $this->agentId++;
        };

        $this->mockery->shouldReceive('AddAgent')->andReturnUsing($mockHandler);
        $this->mockery->shouldReceive('Add')->andReturnUsing(fn () => $this->agentId++);
        $this->mockery->shouldReceive('Delete')->andReturn(fn ($id) => intval($id) >= 1);
        $this->mockery->shouldReceive('RemoveModuleAgents');
        $this->mockery->shouldReceive('RemoveAgent');

        Faker::getInstance()->defer(function() {
            if (!empty($this->disallowed)) {
               throw new DisallowedFakerException('Added unexpected agents: ' . implode(", ", $this->disallowed));
            }
        });

        return null;
    }

    public function expect(string $agentSequence): FakerRule
    {
        Faker::getInstance()->defer(function() use ($agentSequence) {
            $this->assertCalls($agentSequence);
        });

        return $this->allow($agentSequence);
    }

    public function allow(string $agentSequence): FakerRule
    {
        $rule = new AgentFakerRule($agentSequence);
        $this->rules[$agentSequence] = $rule;

        return $rule;
    }

    /**
     * @throws ExpectedFakerException
     */
    public function assertCalls(string $agentSequence): void
    {
        if ($rule = $this->rules[$agentSequence])
        {
            if (!$rule->getCallsCount()) {
                throw new ExpectedFakerException('Expected agent not added');
            }
        }
    }

    public function calls(string $agentSequence): int {
        if ($rule = $this->rules[$agentSequence])
        {
            return $rule->getCallsCount();
        }

        return 0;
    }
}