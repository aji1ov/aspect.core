<?php

namespace Aspect\Lib\Service\Repository;

use Aspect\Lib\Blueprint\Dto\Key;
use Aspect\Lib\Blueprint\Dto\Table;
use Aspect\Lib\Blueprint\Table\Primary;
use Aspect\Lib\Transport\Dto;
use ReflectionProperty;

abstract class RepositoryEntity extends Dto implements EntityInterface
{

    public static function new(): static
    {
        return new static();
    }

    public static function blueprint(): TableBlueprint
    {
        return static::createEntityBlueprint();
    }

    private static function createEntityBlueprint(): TableBlueprint
    {
        $rc = new \ReflectionClass(static::class);
        $className = static::getTableName($rc);
        $fields = static::getTableFields($rc);

        return new TableBlueprint($className, $fields);
    }

    /**
     * @param \ReflectionClass $rc
     * @return FieldBlueprint[]
     */
    private static function getTableFields(\ReflectionClass $rc): array
    {
        $fields = [];

        do {
            foreach ($rc->getProperties() as $property) {
                $fields[] = static::createFieldBlueprint($property);
            }
        } while ($rc = $rc->getParentClass());

        return $fields;
    }

    private static function createFieldBlueprint(ReflectionProperty $property): FieldBlueprint
    {
        $dtoKey = $property->getName();
        $remoteKey = $dtoKey;
        $attributes = [];

        if ($attrs = $property->getAttributes(Key::class)) {
            $key = $attrs[0]->newInstance();
            assert($key instanceof Key);
            $remoteKey = $key->getName();
        }

        if ($property->getAttributes(Primary::class)) {
            $attributes[] = 'primary';
        }

        return new FieldBlueprint($dtoKey, $remoteKey, $attributes);
    }

    private static function getTableName(\ReflectionClass $rc)
    {
        if ($attributes = $rc->getAttributes(Table::class)) {
            $table = $attributes[0]->newInstance();
            assert($table instanceof Table);
            return $table->getTableName();
        }

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $rc->getName()));
    }
}