<?php

namespace Aspect\Lib\Support\Interfaces;

use Aspect\Lib\Exception\CommandException;
use Closure;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CommandDispatcherInterface
{
    /**
     * @throws CommandException
     */
    public function dispatch(string $name, Closure $command, InputInterface $input, OutputInterface $output, InputDefinition $definition): void;
}