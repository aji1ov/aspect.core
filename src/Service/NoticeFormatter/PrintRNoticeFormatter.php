<?php

namespace Aspect\Lib\Service\NoticeFormatter;

use Aspect\Lib\Support\Interfaces\NoticeFormatterInterface;

class PrintRNoticeFormatter implements NoticeFormatterInterface
{

    public function format(...$arguments): string
    {
        return print_r(count($arguments) > 1 ? $arguments : reset($arguments), true);
    }
}