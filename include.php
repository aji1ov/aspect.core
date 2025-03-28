<?php

use Aspect\Lib\Application;
use Aspect\Lib\Exception\CommandException;
use Aspect\Lib\Facade\Command;
use Aspect\Lib\Facade\Queue;
use Aspect\Lib\Service\Background\Job;
use Aspect\Lib\Support\Interfaces\NoticerInterface;
use Carbon\Carbon;
use Symfony\Component\Console\Input\InputInterface;

include_once __DIR__. '/src/functions.php';

function notice(...$arguments): void
{
    Application::getInstance()->get(NoticerInterface::class)->notice(...$arguments);
}

function now(): Carbon
{
    return Carbon::now();
}

/**
 * @throws ReflectionException
 * @throws CommandException
 */
function command(string $command, InputInterface|string|array|null $parameters = null, mixed $output = null): ?string
{
    return Command::call($command, $parameters, $output);
}

/**
 * @throws Exception
 */
function queue(string|callable|Job $job, ?string $taggedQueue = null, int|Carbon|null $startAt = null): void
{
    Queue::dispatch($job, $taggedQueue, $startAt);
}

if (class_exists(Application::class)) {
    Application::getInstance();
}
