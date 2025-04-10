<?php

namespace Aspect\Lib\Service\Console;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\DI\FakeInterface;
use Aspect\Lib\PhpUnit\Faker;
use Aspect\Lib\PhpUnit\Faker\CommandFakerRule;
use Aspect\Lib\PhpUnit\Faker\Exception\DisallowedFakerException;
use Aspect\Lib\PhpUnit\Faker\Exception\ExpectedFakerException;
use Aspect\Lib\PhpUnit\Faker\FakerRule;
use Aspect\Lib\Support\Interfaces\CommandDispatcherInterface;
use Closure;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FakeCommandDispatcher implements CommandDispatcherInterface, FakeInterface
{
    /**
     * @var CommandFakerRule[]
     */
    private array $rules = [];
    private array $disallowed = [];

    #[Fetch]
    private CommandDispatcherInterface $realDispatcher;

    public function __construct()
    {
        Faker::getInstance()->defer(function () {
            if (!empty($this->disallowed)) {
                throw new DisallowedFakerException('Added unexpected agents: ' . implode(", ", $this->disallowed));
            }
        });
    }

    public function dispatch(string $name, Closure $command, InputInterface $input, OutputInterface $output, InputDefinition $definition): void
    {
        if ($rule = $this->rules[$name]) {
            $rule->call($this->realDispatcher, $command, $input, $output, $definition);
        } else {
            $this->disallowed[] = $name;
        }
    }

    /**
     * @param class-string<Command> $name
     */
    public function allow(string $name): FakerRule
    {
        $rule = new CommandFakerRule($name);
        $this->rules[$name::getName()] = $rule;
        return $rule;
    }

    /**
     * @param class-string<Command> $name
     */
    public function expect(string $name): FakerRule
    {
        Faker::getInstance()->defer(function () use ($name) {
            $this->assertCalls($name);
        });
        return $this->allow($name);
    }

    /**
     * @param class-string<Command> $command
     * @throws ExpectedFakerException
     */
    public function assertCalls(string $command): void
    {
        if (($rule = $this->rules[$command::getName()]) && !$rule->getCallsCount()) {
            throw new ExpectedFakerException('Expected command not called');
        }
    }

    /**
     * @param class-string<Command> $command
     */
    public function calls(string $command): int
    {
        if ($rule = $this->rules[$command]) {
            return $rule->getCallsCount();
        }

        return 0;
    }
}