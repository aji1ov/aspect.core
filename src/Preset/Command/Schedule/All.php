<?php

namespace Aspect\Lib\Preset\Command\Schedule;

use Aspect\Lib\Facade\Yakov;
use Aspect\Lib\Service\Background\Event;
use Aspect\Lib\Service\Console\Background;
use Aspect\Lib\Service\Console\Color;
use Aspect\Lib\Service\Console\Command;
use Aspect\Lib\Service\Console\Fake;
use Aspect\Lib\Struct\ConsoleTable;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class All extends Command
{

    use ConsoleTable;

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln($this->withTable(function (Table $table) {

            $table->addRow([
                Color::BLUE->wrap('NAME'),
                Color::DARK_GREY->wrap('Sign'),
                'Mask',
                Color::YELLOW->wrap('Next exec')
            ]);

            /** @var Event $event */
            foreach (include Yakov::getScheduleEventsPath() as $event) {
                $table->addRow([
                    Color::BLUE->wrap($event->getDescription()),
                    Color::DARK_GREY->wrap(Event::sign($event)),
                    implode(" ", $event->getExpression()->getParts()),
                    Color::YELLOW->wrap(date("j.m.Y H:i:s", $event->getCheckTime(time())))
                ]);
            }
        }));
    }

    public static function getDescription(): string
    {
        return "Show all scheduled events";
    }

    public static function structure(): array
    {
        return [];
    }
}