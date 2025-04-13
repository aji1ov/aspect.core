<?php

namespace Aspect\Lib\Preset\Command\Queue;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Service\Background\Job;
use Aspect\Lib\Service\Background\JobInfo;
use Aspect\Lib\Service\Console\Argument;
use Aspect\Lib\Service\Console\Color;
use Aspect\Lib\Service\Console\Command;
use Aspect\Lib\Struct\ConsoleTable;
use Aspect\Lib\Support\Interfaces\JobDispatcherInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class All extends Command
{

    use ConsoleTable;

    #[Fetch]
    protected JobDispatcherInterface $producer;

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $queues = $input->getArgument('queue');
        $output->writeln($this->withTable(function(Table $table) use ($queues) {

            $table->addRow([
                Color::BLUE->wrap('Name'),
                Color::DARK_GREY->wrap('Sign'),
                'Queue',
                Color::YELLOW->wrap('StartAt'),
                'Waiting',
                'Started'
            ]);

            foreach ($this->producer->getInfo($queues) as $job) {
                assert(is_a($job, JobInfo::class, true));

                $color = Color::LIGHT_RED;

                if ($job->isBusy() || unix($job->getStartAt())->diffInMinutes(now()) < 5) {
                    $color = Color::GREEN;
                } else if(unix($job->getStartAt())->diffInHours(now()) < 1) {
                    $color = Color::LIGHT_YELLOW;
                }

                $table->addRow([
                    Color::BLUE->wrap($job->getName()),
                    Color::DARK_GREY->wrap($job->getSign()),
                    $job->getQueue(),
                    Color::YELLOW->wrap(unix($job->getStartAt())->format("j.m.Y H:i:s")),
                    $color->wrap($job->isBusy() ? '-' : now()->diffForHumans(unix($job->getStartAt()), CarbonInterface::DIFF_ABSOLUTE)),
                    $job->isBusy() ? 'Yes' : 'No'
                ]);
            }
        }));

    }

    public static function getDescription(): string
    {
        return 'Show jobs in queue';
    }

    public static function structure(): array
    {
        return [
            static::argument('queue', Argument::OPTIONAL_ARRAY, 'tagged queues')
        ];
    }
}