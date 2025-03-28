<?php

namespace Aspect\Lib\Ajax;

class Redirect extends JsExpression
{
    public function __construct(string $url)
    {
        parent::__construct("document.location.href=\"".$url."\"");
    }
}