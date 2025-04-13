<?php

namespace Aspect\Lib\Facade;

use Aspect\Lib\Application;
use Aspect\Lib\Service\Background\ClosureJob;
use Aspect\Lib\Service\Background\Job;
use Aspect\Lib\Support\Interfaces\JobDispatcherInterface;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;

final class Queue
{
    const COMMON = 'common';
    const COMMAND = 'command';
    const SCHEDULED = 'scheduled';

    /**
     * @param class-string<Job>|Job|callable $job
     * @param string|null $taggedQueue
     * @param int|Carbon|null $startAt
     * @return void
     * @throws Exception
     */
    public static function dispatch(string|callable|Job $job, ?string $taggedQueue = null, int|Carbon|null $startAt = null): void
    {
        $job = Queue::job($job);

        if(!$taggedQueue) {
            $taggedQueue = Queue::COMMON;
        }

        if(!$startAt) {
            $startAt = time();
        } else if($startAt instanceof DateTimeInterface) {
            $startAt = $startAt->unix();
        }

        Application::getInstance()->get(JobDispatcherInterface::class)->dispatch($job, $taggedQueue, $startAt);
    }

    /**
     * @throws Exception
     */
    public static function isDefined(string|callable|Job $job, ?string $taggedQueue = null): bool
    {

        $job = Queue::job($job);

        if(!$taggedQueue) {
            $taggedQueue = Queue::COMMON;
        }

        return Application::getInstance()->get(JobDispatcherInterface::class)->isDefined($job, $taggedQueue);
    }

    public static function job(string|callable|Job $job): Job
    {
        if(is_string($job) && is_a($job, Job::class, true)) {
            $job = Application::getInstance()->get($job);
        } else if(is_callable($job)) {
            $job = new ClosureJob($job);
        }

        assert(is_a($job, Job::class, true));

        return $job;
    }

}