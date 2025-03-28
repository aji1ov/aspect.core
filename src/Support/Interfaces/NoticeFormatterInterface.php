<?php

namespace Aspect\Lib\Support\Interfaces;

interface NoticeFormatterInterface
{
    public function format(...$arguments): string;
}