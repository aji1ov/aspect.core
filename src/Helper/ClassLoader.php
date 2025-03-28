<?php

namespace Aspect\Lib\Helper;

class ClassLoader
{
    public function __construct() {

    }

    public static function scan($pattern, $flags = 0)
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

    public function getClasses(array $folders, array $namespaces, ?callable $filter = null): array
    {
        $classes = [];
        foreach ($folders as $folder) {

            if(!$folder) {
                continue;
            }

            foreach (static::scan($folder."*.php") as $path) {
                $tryName = str_replace([".php", "/"], ["", "\\"],str_replace($folder, "", $path));
                foreach ($namespaces as $namespace) {
                    if($className = $this->tryClass($tryName, $namespace)) {
                        if(!$filter || $filter($className)) {
                            $classes[] = $className;
                        }
                    }
                }
            }
        }
        return array_unique($classes);
    }

    public function getClassesFromYakov(array $yakovData, ?callable $filter = null): array
    {
        return $this->getClasses(
            array_values($yakovData),
            array_keys($yakovData),
            $filter
        );
    }

    private function tryClass(string $tryName, string $namespace): ?string
    {
         if(class_exists($namespace . $tryName)) {
             return $namespace . $tryName;
         }

         return null;
    }
}