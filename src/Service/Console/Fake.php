<?php

namespace Aspect\Lib\Service\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\TrimmedBufferOutput;

class Fake
{
    public static function makeInputFromArray(array $parameters = []): InputInterface
    {
       return new ArrayInput($parameters ?: []);
    }

    public static function makeInputFromString(string $parameter): InputInterface
    {
        return new StringInput($parameter);
    }

    public static function makeTextOutput(int $outputSize = 1048576): TrimmedBufferOutput
    {
        return new TrimmedBufferOutput($outputSize);
    }

    public static function makeOutputFromResource($resource): OutputInterface
    {
        return new StreamOutput($resource);
    }
}