<?php

namespace Aspect\Lib\Struct;

use Aspect\Lib\Struct\Blueprint\PropertyBlueprint;
use ReflectionClass;
use ReflectionException;

class ClassBlueprint
{

    private ReflectionClass $class;

    /**
     * @var PropertyBlueprint[]|null
     */
    private ?array $properties = null;

    /**
     * @param class-string $className
     * @throws ReflectionException
     */
    public function __construct(string $className)
    {
        $this->class = new ReflectionClass($className);
    }

    /**
     * @return PropertyBlueprint[]
     */
    private function properties(): array
    {
        if (!$this->properties) {
            $class = $this->class;

            do {
                foreach ($class->getProperties() as $property) {
                    $this->properties[] = new PropertyBlueprint($property);
                }
            } while ($class = $class->getParentClass());
        }

        return $this->properties;
    }
}