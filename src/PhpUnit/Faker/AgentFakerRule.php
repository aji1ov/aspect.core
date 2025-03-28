<?php

namespace Aspect\Lib\PhpUnit\Faker;

use Aspect\Lib\PhpUnit\Faker\Dto\AddAgentParametersDto;

class AgentFakerRule extends FakerRule
{
    private string $agentSequence;

    public function __construct(string $agentSequence)
    {
        $this->agentSequence = $agentSequence;
    }

    public function call(AddAgentParametersDto $parameters): void
    {
        $this->addCall();
        if ($this->fakeHandler) {
            $fakeHandler = $this->fakeHandler;
            $fakeHandler($parameters);

        } else if ($this->autorun) {
            $handler = $this->agentSequence;
            $handler();
        }
    }
}