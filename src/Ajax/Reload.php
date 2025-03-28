<?php

namespace Aspect\Lib\Ajax;

class Reload extends JsExpression
{
    public function __construct()
    {
        parent::__construct("document.location.reload()");
    }
}