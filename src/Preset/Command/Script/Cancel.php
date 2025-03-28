<?php

namespace Aspect\Lib\Preset\Command\Script;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Manager\ScriptManager;
use Aspect\Lib\Service\Console\Argument;
use Aspect\Lib\Service\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Cancel extends Command
{

    #[Fetch]
    public ScriptManager $manager;

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $name = $input->getArgument('name');

        if (!$this->manager->isRunning($name)) {
            $output->writeln("Script is not a running");
            return;
        }

        $this->manager->cancel($name);
        $output->writeln("Cancelled");
    }

    public static function getDescription(): string
    {
        return "Cancel running script";
    }

    public static function structure(): array
    {
        return [
            static::argument('name', Argument::REQUIRED, 'name of script file')
        ];
    }
}