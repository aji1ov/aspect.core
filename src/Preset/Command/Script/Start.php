<?php

namespace Aspect\Lib\Preset\Command\Script;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Manager\ScriptManager;
use Aspect\Lib\Table\ScriptTable;
use Aspect\Lib\Service\Console\Argument;
use Aspect\Lib\Service\Console\Command;
use Aspect\Lib\Support\Interfaces\ScriptManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Start extends Command
{

    #[Fetch]
    protected ScriptManagerInterface $scriptManager;

    #[Fetch]
    public ScriptManager $manager;

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $name = $input->getArgument('name');

        if ($this->manager->register($name)) {
            $output->writeln("Start");

            $this->scriptManager->observe($name);
            $this->manager->finish($name);

            $output->writeln("Done");
        } else {
            $output->writeln("Script already running");
        }
    }

    public static function getDescription(): string
    {
        return "init Script";
    }

    public static function structure(): array
    {
        return [
            static::argument('name', Argument::REQUIRED, 'name of script file')
        ];
    }
}