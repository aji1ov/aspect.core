<?php

namespace Aspect\Lib\Support\Interfaces;

use Aspect\Lib\Service\Background\Event;

interface ScheduleExecutorInterface
{
    /**
     * @param Event[] $events
     * @return void
     */
    public function install(array $events): void;
}