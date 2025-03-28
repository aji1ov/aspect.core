<?php

namespace Aspect\Lib\Struct;

use Aspect\Lib\Blueprint\Ignore;
use Aspect\Lib\Blueprint\Pretty;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use UnitEnum;

trait PrettyPrint
{

    public function __debugInfo()
    {
        $c = new ReflectionClass(static::class);
        return array_merge([], $this->prettyProperties($c), $this->prettyMethods($c));
    }

    private function prettyProperties(ReflectionClass $c)
    {
        $result = [];
        foreach ($c->getProperties() as $property) {

            if ($property->getAttributes(Ignore::class)) {
                continue;
            }

            if (!$property->isPrivate() || $property->getAttributes(Pretty::class)) {
                $name = $this->prettyName($property);

                $value = $property->getValue($this);
                if ($value instanceof UnitEnum) {
                    $value = $this->prettyEnum($value);
                }

                $result[$name] = $value;
            }
        }

        return $result;
    }

    private function prettyName(ReflectionProperty|ReflectionMethod $entity): string
    {
        $name = match (true) {
            $entity->isPrivate() => "private:" . $entity->name,
            $entity->isProtected() => "protected:" . $entity->name,
            default => $entity->name
        };

        if ($entity->isStatic()) {
            $name = 'static ' . $name;
        }

        if ($entity instanceof ReflectionMethod) {
            $parameters = [];
            foreach ($entity->getParameters() as $parameter) {
                $parameterName = '$' . $parameter->name;

                if ($parameterType = $parameter->getType()) {
                    if ($parameterType instanceof ReflectionUnionType) {
                        $parameterTypes = implode("|", array_map(fn(ReflectionNamedType $type) => $type->getName(), $parameterType->getTypes()));
                        $parameterName = $parameterTypes . ' ' . $parameterName;
                    } else {
                        $parameterName = $parameterType->getName() . ' ' . $parameterName;
                    }

                }

                $parameters[] = $parameterName;
            }
            $name .= '(' . implode(", ", $parameters) . ')';
        }

        return $name;
    }

    private function prettyEnum(UnitEnum $unit): string
    {
        $enum = get_class($unit);
        $result = "Enum " . $enum . "::" . $unit->name;

        if ($unit->value) {
            $result .= " (" . $unit->value . ")";
        }

        return $result;
    }

    private function prettyMethods(ReflectionClass $c): array
    {
        $result = [];
        foreach ($c->getMethods() as $method) {
            if ($method->isInternal() || str_starts_with($method->name, '__') || $method->getAttributes(Ignore::class)) {
                continue;
            }

            if (!$method->isPrivate() || $method->getAttributes(Pretty::class)) {
                $name = $this->prettyName($method);
                $result[$name] = $method->getReturnType()?->getName() ?? 'mixed';
            }
        }

        return $result;
    }
}
