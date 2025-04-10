<?php

namespace Aspect\Lib\Service\Repository;

class TableBlueprint
{
    private ?string $tableName;

    /**
     * @var FieldBlueprint[]
     */
    private array $fields = [];
    private ?FieldBlueprint $primary = null;

    /**
     * @param string $tableName
     * @param FieldBlueprint[] $fields
     */
    public function __construct(string $tableName, array $fields)
    {
        $this->tableName = $tableName;
        $this->fields = $fields;

        foreach ($fields as $field) {
            if ($field->isPrimary()) {
                $this->primary = $field;
            }
        }
    }

    public function primary(): FieldBlueprint|null
    {
        return $this->primary;
    }
}