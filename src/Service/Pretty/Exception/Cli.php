<?php

namespace Aspect\Lib\Service\Pretty\Exception;

use Aspect\Lib\Service\Console\Background;
use Aspect\Lib\Service\Console\Color;
use Aspect\Lib\Service\Console\Fake;
use Aspect\Lib\Service\Pretty\Exception;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Throwable;

class Cli implements Exception
{

    public function makePretty(Throwable $throwable): string
    {
        $result = Color::YELLOW->wrap("Uncaught exception in " . $throwable->getFile() . " line " . $throwable->getLine());

        $output = Fake::makeTextOutput();
        $style = new TableStyle();
        $style->setBorderFormat('');
        $table = new Table($output);
        $table->setStyle($style);

        $table->addRows([
            [Color::BLACK->wrap('Message'), $throwable->getMessage()],
            [Color::BLACK->wrap('Class'), get_class($throwable)]
        ]);

        $table->render();

        $result .= Background::LIGHT_RED->wrap(
                Color::LIGHT_GREY->wrap(
                    "\n" . trim($output->fetch(), "\n\r\t\v\0")
                )
            ) . "\n";

        $result .= Color::DARK_GREY->wrap($throwable->getTraceAsString());

        return $result;
    }
}