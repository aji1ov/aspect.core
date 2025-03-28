<?php

namespace Aspect\Lib\Preset\Command\Script;

use Aspect\Lib\Facade\Yakov;
use Aspect\Lib\Service\Console\Argument;
use Aspect\Lib\Service\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Exec extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $name = $input->getArgument('name');
        $scriptPath = Yakov::getCmdBundlesPath().$name.".php";

        include $scriptPath;
    }

    public static function getDescription(): string
    {
        return 'Exec script';
    }

    public static function structure(): array
    {
        return [
            static::argument('name', Argument::REQUIRED, 'name of script file')
        ];
    }
}