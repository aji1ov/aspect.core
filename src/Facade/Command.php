<?php

namespace Aspect\Lib\Facade;

use Aspect\Lib\Application;
use Aspect\Lib\Exception\CommandException;
use Aspect\Lib\Service\Background\CommandJob;
use Aspect\Lib\Service\Console\CommandLocator;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;
use ReflectionException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\TrimmedBufferOutput;
use Aspect\Lib\Service\Console\Command as CommandExec;

final class Command
{
    /**
     * @param string|class-string<CommandExec> $command
     * @param InputInterface|string|array|null $parameters
     * @param OutputInterface|resource|null $output
     * @return string|null
     * @throws CommandException
     * @throws ReflectionException
     * @throws Exception
     */
    public static function call(string $command, InputInterface|string|array|null $parameters = null, mixed $output = null): ?string
    {
        if ($commandObject = Command::getInstance($command)) {

            $_output = Command::normalizeOutput($output);

            CommandExec::callNoWait(
                $commandObject,
                Command::normalizeInput($parameters),
                $_output
            );

            if ($_output instanceof TrimmedBufferOutput) {
                return $_output->fetch();
            }

            return null;
        }

        throw new CommandException('Command `' . $command . '` not found');
    }

    /**
     * @param string|class-string<CommandExec> $command
     * @param InputInterface|string|array|null $parameters
     * @param int|DateTimeInterface|null $startAt
     * @throws Exception
     */
    public static function later(string $command, InputInterface|string|array|null $parameters = null, int|DateTimeInterface|null $startAt = null): void
    {
        Queue::dispatch(new CommandJob($command, $parameters), Queue::COMMAND, $startAt);
    }

    /**
     * @throws Exception
     */
    public static function inQueue(string $command, InputInterface|string|array|null $parameters = null): bool
    {
        return Queue::isDefined(new CommandJob($command, $parameters), Queue::COMMAND);
    }

    /**
     * @param string|class-string<CommandExec> $command
     * @return CommandExec|null
     * @throws Exception
     */
    public static function getInstance(string $command): ?CommandExec
    {
        if (is_a($command, CommandExec::class, true)) {
            $commandObject = Application::getInstance()->get($command);
        } else {
            $commandObject = Application::getInstance()->get(CommandLocator::class)->locate($command);
        }

        return $commandObject;
    }

    private static function normalizeInput(InputInterface|string|array|null $parameters = null): InputInterface
    {
        if ($parameters instanceof InputInterface) {
            return $parameters;
        }

        if (is_string($parameters)) {
            return new StringInput($parameters);
        }

        return Command::makeInput($parameters);
    }

    private static function makeInput(?array $parameters = null): InputInterface
    {
        return new ArrayInput($parameters ?: []);
    }

    /**
     * @param OutputInterface|resource|null $output
     */
    private static function normalizeOutput(mixed $output = null): OutputInterface
    {
        if ($output instanceof OutputInterface) {
            return $output;
        }

        if (is_resource($output)) {
            return new StreamOutput($output);
        }

        return new TrimmedBufferOutput(1048576);
    }
}