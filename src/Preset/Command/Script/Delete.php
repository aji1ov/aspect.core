<?php

namespace Aspect\Lib\Preset\Command\Script;

use Aspect\Lib\Facade\Yakov;
use Aspect\Lib\Manager\ScriptManager;
use Aspect\Lib\Service\Console\Argument;
use Aspect\Lib\Service\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Delete extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $name = \CUtil::translit($input->getArgument('name'), 'ru');
        unlink(Yakov::getCmdBundlesPath().$name.".php");
        ScriptManager::getInstance()->clear($name);
    }

    public static function getDescription(): string
    {
        return "delete script file";
    }

    public static function structure(): array
    {
        return [
            static::argument('name', Argument::REQUIRED, 'script name')
        ];
    }
}