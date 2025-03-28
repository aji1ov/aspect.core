<?php

namespace Aspect\Lib\Service\Noticer;

use Aspect\Lib\Service\Console\Color;

class CliNoticer extends FormattedNoticer
{
    public function notice(...$arguments): void
    {
        fwrite(STDOUT, Color::YELLOW->wrap("[".date("j.m.Y H:i:s")."] ").$this->formatter->format(...$arguments)."\n");
    }
}