<?php

namespace Aspect\Lib\Preset\Command\Worker;

use Aspect\Lib\Preset\Command\Schedule\Update;
use Aspect\Lib\Service\Console\Argument;
use Aspect\Lib\Service\Console\Command;
use Aspect\Lib\Service\Console\Option;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Cron extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        command('schedule.update');
        command('queue.run', ['--time' => $input->getOption('time'), '-n' => true]);

    }

    public static function getDescription(): string
    {
        return 'Run schedule and queue from cron interface';
    }

    public static function structure(): array
    {
        return [
            static::option('time', 't',Option::OPTIONAL, 'Limit of execution in seconds', 45),
        ];
    }
}