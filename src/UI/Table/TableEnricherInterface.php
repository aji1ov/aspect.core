<?php

namespace Aspect\Lib\UI\Table;

use Aspect\Lib\UI\Table;

interface TableEnricherInterface
{
    public function getTableComponentParameters(Table $table): array;
}