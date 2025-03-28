<?php

namespace Aspect\Lib\UI\Table;

interface TableRowInterface
{
    public function toArray(array $row): array;
}