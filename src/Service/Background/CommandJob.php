<?php

namespace Aspect\Lib\Service\Background;

use Aspect\Lib\Application;
use Aspect\Lib\Exception\CommandException;
use Aspect\Lib\Facade\Command;
use Aspect\Lib\Service\Console\Fake;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use ReflectionException;

class CommandJob extends Job
{
    private string $command;
    private string $parameter;

    public function __construct(string $command, InputInterface|string|array|null $parameters = null)
    {
        $this->command = $command;
        if ($parameters instanceof InputInterface) {
            $parameters = $parameters->__toString();
        }

        if (is_array($parameters)) {
            $parameters = (new ArrayInput($parameters))->__toString();
        }

        $this->parameter = $parameters ?? '';
    }

    /**
     * @throws ReflectionException
     * @throws CommandException
     */
    public function handle(): void
    {
        $output = Fake::makeTextOutput();
        Command::call($this->command, Fake::makeInputFromString($this->parameter), $output);
        $this->logger->notice($output->fetch());
    }

    public function getName(): string
    {
        if($command = Command::getInstance($this->command)) {
            return $command::getDescription();
        }

        return parent::getName();
    }
}