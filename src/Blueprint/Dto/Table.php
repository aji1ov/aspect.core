<?php

namespace Aspect\Lib\Blueprint\Dto;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Table
{
    private string $tableName;
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }
}