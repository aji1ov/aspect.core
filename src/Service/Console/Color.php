<?php

namespace Aspect\Lib\Service\Console;

enum Color: int
{
    case BLACK = 30;
    case RED = 31;
    case GREEN = 32;
    case YELLOW = 33;
    case BLUE = 34;
    case MAGENTA = 35;
    case CYAN = 36;
    case LIGHT_GREY = 37;

    case DEFAULT = 39;

    case DARK_GREY = 90;

    case LIGHT_RED = 91;
    case LIGHT_GREEN = 92;
    case LIGHT_YELLOW = 93;
    case LIGHT_BLUE = 94;
    case LIGHT_MAGENTA = 95;
    case LIGHT_CYAN = 96;

    case WHITE = 97;

    public function make(): string
    {
        return "\e[".$this->value."m";
    }
    
    public function wrap(string $inner): string 
    {
        return $this->make().$inner.Color::DEFAULT->make();
    }

    public static function clean(string $input): string
    {
        return preg_replace("/\e\[\d{2}m/", "", $input);
    }
}
