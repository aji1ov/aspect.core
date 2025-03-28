<?php

namespace Aspect\Lib\Service\Console;

use Aspect\Lib\Exception\CommandException;
use Aspect\Lib\Support\Interfaces\CommandDispatcherInterface;
use Closure;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandDispatcher implements CommandDispatcherInterface
{



    /**
     * @throws CommandException
     */
    public function dispatch(string $name, Closure $command, InputInterface $input, OutputInterface $output, InputDefinition $definition): void
    {
        try {
            $input->bind($definition);
            $input->validate();
        }
        catch(ExceptionInterface $e)
        {
            throw new CommandException($e->getMessage());
        }

        $command($input, $output);
    }
}