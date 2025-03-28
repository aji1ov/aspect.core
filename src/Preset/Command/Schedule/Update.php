<?php

namespace Aspect\Lib\Preset\Command\Schedule;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Facade\Yakov;
use Aspect\Lib\Service\Console\Command;
use Aspect\Lib\Support\Interfaces\ScheduleExecutorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{

    #[Fetch]
    protected ScheduleExecutorInterface $executor;

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->executor->install(include Yakov::getScheduleEventsPath());
    }

    public static function getDescription(): string
    {
        return "Add planned jobs to queue";
    }

    public static function structure(): array
    {
        return [];
    }
}