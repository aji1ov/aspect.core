<?php

namespace Aspect\Lib\Preset\Command\Queue;

use Aspect\Lib\Application;
use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Context;
use Aspect\Lib\Facade\Queue;
use Aspect\Lib\Service\Console\Color;
use Aspect\Lib\Service\Console\Command;
use Aspect\Lib\Service\Console\Option;
use Aspect\Lib\Support\Interfaces\JobDispatcherInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{

    #[Fetch]
    private JobDispatcherInterface $producer;

    public static function getDescription(): string
    {
        return 'Run queue of background tasks';
    }

    public static function structure(): array
    {
        return [
            static::option('time', 't', Option::OPTIONAL, 'Seconds of execute', 30),
            static::option('max', 'm', Option::OPTIONAL, 'Max jobs (0 - unlimited)', 0),
            static::option('nowait', 'n', Option::NONE, 'Exit if no jobs in queue'),
            static::option('queue', 'q', Option::OPTIONAL_ARRAY, 'Take only jobs in tagged queue'),
            static::option('slow', 's', Option::NONE, 'Take only oldest jobs')
        ];
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $startAt = time();

        $seconds = (int)$input->getOption('time');
        $max = (int)$input->getOption('max');
        $nowait = $input->getOption('nowait');
        $queues = $input->getOption('queue');
        $slow = $input->getOption('slow');

        if(!$seconds) {
            $seconds = PHP_INT_MAX;
        }

        if ($max === 0) {
            $max = PHP_INT_MAX;
        }

        $stopAt = $startAt + $seconds;
        $provider = $this->producer->getProvider($queues);

        while (time() < $stopAt && $max > 0) {
            if ($job = $provider->next($slow ? $startAt : time())) {
                $success = Application::getInstance()->bound(
                    Context::CRON,
                    fn () => $provider->handle($job)
                );

                notice("Job ".Color::BLUE->wrap("[".$job->getName()."]")." -> ".($success ? Color::GREEN->wrap("Success") : Color::RED->wrap("Failed")));
                $max -= 1;
            } else if ($nowait) {
                break;
            } else {
                notice(Color::DARK_GREY->wrap("No job to process, sleeping 1 sec"));
                sleep(1);
                continue;
            }

            sleep(.01);
        }


        //$provider = $this->producer->getProvider();
    }
}