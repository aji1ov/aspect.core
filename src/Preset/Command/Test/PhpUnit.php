<?php

namespace Aspect\Lib\Preset\Command\Test;

use Aspect\Lib\Service\Console\Color;
use Aspect\Lib\Service\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpUnit extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $handle = popen('local/php_interface/vendor/bin/phpunit -c local/modules/aspect.core/phpunit.xml --colors=always --testdox', 'r');
        while(!feof($handle)) {
            $read = fread($handle, 1024);
            $output->write($read);
        }

        $output->write("\n");
        pclose($handle);
    }

    public static function getDescription(): string
    {
        return "Run phpunit tests";
    }

    public static function structure(): array
    {
        return [];
    }
}