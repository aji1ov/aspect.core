<?php

namespace Aspect\Lib\Ajax;

use Bitrix\Main\LoaderException;
use Bitrix\Main\UI\Extension;

abstract class DependedExpression extends Callback
{

    public function withDependency(string ...$dependencies): static
    {
        foreach ($dependencies as $dependency) {
            try {
                Extension::load($dependency);
            } catch (LoaderException $e) {

            }
        }

        return $this;
    }
}