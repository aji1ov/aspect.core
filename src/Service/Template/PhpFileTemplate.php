<?php

namespace Aspect\Lib\Service\Template;

class PhpFileTemplate
{
    private array $keys = [];

    private function __construct(array $keys)
    {
        $this->keys  = $keys;
    }

    public static function with(array $keys): static
    {
        return new PhpFileTemplate($keys);
    }

    public function compile(string $path): ?string
    {
        $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$path);
        foreach ($this->keys as $key => $value) {
            $content = str_replace('$'.$key, $value, $content);
        }

        return $content;
    }

    public function insert(string $from, string $to): void
    {
        file_put_contents($to, $this->compile($from));
    }
}