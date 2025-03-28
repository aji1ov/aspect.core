<?php

namespace Aspect\Lib\Service\Console;

use Aspect\Lib\Application;
use Aspect\Lib\Blueprint\Command\Lockable;
use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Exception\CommandException;
use Aspect\Lib\Exception\LockedCommandException;
use Aspect\Lib\Facade\Yakov;
use Aspect\Lib\Support\Interfaces\CommandDispatcherInterface;
use Exception;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command
{
    use LockableTrait;

    #[Fetch]
    protected LoggerInterface $logger;

    abstract protected function execute(InputInterface $input, OutputInterface $output): void;
    abstract public static function getDescription(): string;

    /**
     * @return InputArgument[]|InputOption[]
     */
    abstract public static function structure(): array;

    public final static function getName(): string
    {
        foreach (Yakov::getPathToCommands() as $namespace => $folder) {
            if(str_starts_with(static::class, $namespace)) {
                return strtolower(str_replace('\\', '.', str_replace($namespace, '', static::class)));
            }
        }
        return strtolower(str_replace('\\', '.', static::class));
    }

    /**
     * @throws CommandException
     * @throws ReflectionException
     */
    public static final function call(Command $command, InputInterface $input, OutputInterface $output): void
    {
        static::callInternal($command, $input, $output);
    }

    /**
     * @throws ReflectionException
     * @throws CommandException
     */
    public static final function callNoWait(Command $command, InputInterface $input, OutputInterface $output): void
    {
        static::callInternal($command, $input, $output, false);
    }

    /**
     * @throws CommandException
     * @throws ReflectionException
     * @throws Exception
     */
    private static function callInternal(Command $command, InputInterface $input, OutputInterface $output, $wait = true): void
    {
        $lockable = static::isLockable($command);

        static::beforeCall($command, $lockable, $wait);

        $definition = static::getDefinition($command);
        $execute = fn (InputInterface $input, OutputInterface $output) => $command->execute($input, $output);

        $dispatcher = Application::getInstance()->get(CommandDispatcherInterface::class);
        $dispatcher->dispatch($command::getName(), $execute, $input, $output, $definition);

        static::afterCall($command, $lockable);
    }

    /**
     * @throws LockedCommandException
     */
    private static function beforeCall(Command $command, bool $lockable, bool $wait): void
    {
        if($lockable) {
            if($command->lock($command->getName(), $wait)) {
                throw new LockedCommandException("Command is already running in another process");
            }
        }
    }

    private static function afterCall(Command $command, bool $lockable): void
    {
        if($lockable) {
            $command->release();
        }
    }

    /**
     * @throws ReflectionException
     */
    public static final function isLockable(Command $command): bool
    {
        $rc = new \ReflectionClass($command::class);
        return (bool) $rc->getAttributes(Lockable::class);
    }

    protected static function argument(string $name, Argument $mode = Argument::OPTIONAL, ?string $description = null, mixed $value = null): InputArgument
    {
        return new InputArgument($name, $mode?->value ?? null, $description ?? '', $value);
    }

    protected static function option(string $name, string|array|null $shortcut = null, Option $mode = Option::OPTIONAL, ?string $description = null, mixed $value = null): InputOption
    {
        return new InputOption($name, $shortcut, $mode?->value ?? null, $description ?? '', $value);
    }

    /**
     * @param Command|class-string<Command> $command
     * @return InputDefinition
     */
    public static function getDefinition(Command|string $command): InputDefinition
    {
        $definition = new InputDefinition();
        foreach ($command::structure() as $argumentOrOption) {
            if($argumentOrOption instanceof InputArgument) {
                $definition->addArgument($argumentOrOption);
            } else if ($argumentOrOption instanceof InputOption) {
                $definition->addOption($argumentOrOption);
            }
        }

        return $definition;
    }

    protected function colorize(Color $color, string $input): string
    {
        return $color->make().$input.Color::DEFAULT->make();
    }
}