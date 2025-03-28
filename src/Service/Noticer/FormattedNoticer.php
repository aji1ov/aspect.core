<?php

namespace Aspect\Lib\Service\Noticer;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Support\Interfaces\NoticeFormatterInterface;
use Aspect\Lib\Support\Interfaces\NoticerInterface;

abstract class FormattedNoticer implements NoticerInterface
{
    #[Fetch]
    protected NoticeFormatterInterface $formatter;
}