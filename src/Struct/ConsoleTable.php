<?php

namespace Aspect\Lib\Struct;

use Aspect\Lib\Service\Console\Fake;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

trait ConsoleTable
{
    protected function withTable(callable $closure): string
    {
        $tableOutput = Fake::makeTextOutput();
        $table = new Table($tableOutput);
        $style = new TableStyle();
        $style->setBorderFormat('');
        $table->setStyle($style);

        $closure($table);

        $table->render();
        return trim($tableOutput->fetch(), "\n\r\t\v\0");
    }
}