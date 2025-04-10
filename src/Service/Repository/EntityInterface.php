<?php

namespace Aspect\Lib\Service\Repository;

use Aspect\Lib\Transport\Transportable;

interface EntityInterface extends Transportable
{
    public static function blueprint(): TableBlueprint;
}