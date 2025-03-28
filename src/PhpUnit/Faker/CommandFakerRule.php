<?php

namespace Aspect\Lib\PhpUnit\Faker;

use Aspect\Lib\Exception\CommandException;
use Aspect\Lib\Service\Console\Command;
use Aspect\Lib\Support\Interfaces\CommandDispatcherInterface;
use Closure;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandFakerRule extends FakerRule
{
    /** @var class-string<Command>  */
    private string $command;

    /**
     * @param class-string<Command> $command
     */
    public function __construct(string $command)
    {
        $this->command = $command;
    }

    /**
     * @throws CommandException
     */
    public function call(CommandDispatcherInterface $dispatcher, Closure $command, InputInterface $input, OutputInterface $output, InputDefinition $definition): void
    {
        $this->addCall();

        if ($this->autorun) {

            $handler = $command;
            if($this->fakeHandler) {
                $handler = $this->fakeHandler;
            }

            $dispatcher->dispatch($this->command::getName(), $handler, $input, $output, $definition);
        }
    }
}