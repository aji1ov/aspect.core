<?php

namespace Aspect\Lib\DI\Serializer;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Blueprint\Ignore;
use ReflectionClass;

class ObjectSerializer
{
    public static function getSerializedProperties($object): array
    {
        $rc = new ReflectionClass(get_class($object));
        return array_unique(static::getSerializedPropertiesFromRC($rc));
    }

    private static function getSerializedPropertiesFromRC(ReflectionClass $class): array
    {
        $properties = [];

        foreach ($class->getProperties() as $property) {
            if (!$property->getAttributes(Ignore::class) && !$property->getAttributes(Fetch::class)) {
                $properties[] = $property->getName();
            }
        }

        if ($parentClass = $class->getParentClass()) {
            $properties = array_merge($properties, static::getSerializedPropertiesFromRC($parentClass));
        }

        return $properties;
    }
}