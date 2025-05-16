<?php

namespace Aspect\Lib\Service\Console;

use Aspect\Lib\Application;
use Aspect\Lib\Facade\Yakov;
use Aspect\Lib\Helper\ClassLoader;
use Exception;

class CommandLocator
{
    /**
     * @throws Exception
     */
    public function locateOrFallback(string $command, Command $fallback): Command
    {
        if($foundCommand = $this->locate($command)) {
            return $foundCommand;
        }

        return $fallback;
    }

    /**
     * @param string $command
     * @return Command|null
     * @throws Exception
     */
    public function locate(string $command): ?Command
    {
        $commands = $this->listOfCommands();
        if(isset($commands[$command])) {
            return Application::getInstance()->get($commands[$command]);
        }
        return null;
    }

    /**
     * @return class-string<Command>[]
     */
    public function listOfCommands(): array
    {

        $classes = (new ClassLoader())->getClassesFromYakov(
            Yakov::getPathToCommands(),
            fn ($className) => is_a($className, Command::class, true)
        );

        $list = [];
        foreach ($classes as $className) {
            assert(is_a($className, Command::class, true));
            $list[$className::getName()] = $className;
        }

        return $list;
    }

    public function groupOfCommands(): array
    {
        $tree = [];
        $list = $this->listOfCommands();
        natsort($list);

        foreach ($list as $name => $command) {

            $nameParts = explode(".", $name);
            if(count($nameParts) === 1) {
                $tree[$name] = ['COMMAND' => $command];
            } else {
                $head = &$tree;
                $fullname = [];
                do {

                    $section = array_shift($nameParts);
                    $fullname[] = $section;

                    if(!isset($head[implode(".", $fullname)]['SECTION'])) {
                        $head[implode(".", $fullname)]['SECTION'] = [];
                    }


                    $head = &$head[implode(".", $fullname)]['SECTION'];

                } while (count($nameParts) > 1);

                $fullname[] = array_shift($nameParts);
                $head[implode(".", $fullname)]['COMMAND'] = $command;
            }
        }

        return $tree;
    }

    public static function scan($pattern, $flags = 0): array
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge(
                [],
                ...[$files, static::scan($dir . "/" . basename($pattern), $flags)]
            );
        }
        return $files;
    }
}