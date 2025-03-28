<?php

namespace Aspect\Lib\Facade;

use Aspect\Lib\Service\Background\ClosureJob;
use Aspect\Lib\Service\Background\CommandJob;
use Aspect\Lib\Service\Background\Event;
use Aspect\Lib\Service\Background\Job;
use Symfony\Component\Console\Input\InputInterface;

final class Schedule
{
    /**
     * @param string|class-string<\Aspect\Lib\Service\Console\Command> $command
     * @param InputInterface|string|array|null $parameters
     * @return Event
     */
    public static function command(string $command, InputInterface|string|array|null $parameters = null): Event
    {
        return new Event(new CommandJob($command, $parameters));
    }

    /**
     * @param class-string<Job>|Job|callable $job
     * @return Event
     */
    public static function job(string|callable|Job $job): Event
    {
        return new Event(Queue::job($job));
    }

    public static function call(callable $closure): Event
    {
        return new Event(new ClosureJob($closure));
    }
}