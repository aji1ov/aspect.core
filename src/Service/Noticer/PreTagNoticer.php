<?php

namespace Aspect\Lib\Service\Noticer;

class PreTagNoticer extends FormattedNoticer
{

    public function notice(...$arguments): void
    {
        echo '<pre>';
        echo $this->formatter->format(...$arguments);
        echo '</pre>';
    }
}