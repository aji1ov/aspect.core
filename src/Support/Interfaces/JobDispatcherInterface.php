<?php

namespace Aspect\Lib\Support\Interfaces;

use Aspect\Lib\Service\Background\Job;
use Aspect\Lib\Service\Background\JobInfo;

interface JobDispatcherInterface
{
    public function dispatch(Job $job, string $queue, int $startAt);
    public function isDefined(Job $job, string $queue): bool;
    public function getProvider(?array $queues): JobProviderInterface;

    /**
     * @param array|null $queues
     * @return JobInfo[]
     */
    public function getInfo(?array $queues): array;
}