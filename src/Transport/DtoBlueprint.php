<?php

namespace Aspect\Lib\Transport;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Blueprint\Dto\Convert;
use Aspect\Lib\Blueprint\Dto\Extension;
use Aspect\Lib\Blueprint\Ignore;
use Aspect\Lib\Support\Interfaces\ConverterInterface;
use Aspect\Lib\Transport\Blueprint\BuiltinProperty;
use Aspect\Lib\Transport\Blueprint\DtoProperty;
use Aspect\Lib\Transport\Blueprint\EnumProperty;
use Aspect\Lib\Transport\Blueprint\ExtensionProperty;
use Aspect\Lib\Transport\Blueprint\UnionProperty;
use Exception;
use IteratorAggregate;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use Traversable;

class DtoBlueprint implements IteratorAggregate
{

    private ReflectionClass $dtoClass;
    private array $map = [];

    /**
     * @var ConverterInterface[]
     */
    private static array $autoConverter = [];

    public static function setConverter(string $targetTypeClass, ConverterInterface $autoConverter): void
    {
        static::$autoConverter[$targetTypeClass] = $autoConverter;
    }

    /**
     * @param class-string<Transportable> $dtoClass
     * @throws ReflectionException
     * @throws Exception
     */
    public function __construct(string $dtoClass)
    {
        $this->dtoClass = new ReflectionClass($dtoClass);
        $this->makeMap();
    }

    /**
     * @throws Exception
     */
    private function makeMap(): void
    {
        $this->parseClass($this->dtoClass);
    }

    /**
     * @throws Exception
     */
    private function parseClass(ReflectionClass $class): void
    {
        $released = [];
        do {
            foreach ($class->getProperties() as $property) {
                if(!$released[$property->name]) {
                    $this->parseProperty($property, $this->map);
                    $released[$property->name] = true;
                }
            }
        } while ($class = $class->getParentClass());
    }

    /**
     * @throws Exception
     */
    private function applyPropertyType(ReflectionProperty $property, ReflectionNamedType $type, array &$map): void
    {
        if ($type->isBuiltin()) {
            $map[] = new BuiltinProperty($property, $type);
            return;
        }

        if ($converter = static::$autoConverter[$type->getName()]) {
            $map[] = new ExtensionProperty($property, $type, [new Convert($converter)]);
            return;
        }

        if (is_a($type->getName(), Transportable::class, true)) {
            $map[] = new DtoProperty($property, $type);
            return;
        }

        if (enum_exists($type->getName())) {
            $map[] = new EnumProperty($property, $type);
            return;
        }

        throw new Exception("Unsupported Dto property `" . $property->getName() . "`");
    }

    /**
     * @throws Exception
     */
    private function parsePropertyType(ReflectionProperty $property, \ReflectionType $type, array &$map): void
    {
        if ($type instanceof ReflectionNamedType) {
            $this->applyPropertyType($property, $type, $map);
            return;

        } else if ($type instanceof \ReflectionUnionType) {

            $allBuiltIn = true;
            foreach ($type->getTypes() as $unionType) {
                if (!$unionType instanceof ReflectionNamedType || !$unionType->isBuiltin()) {
                    $allBuiltIn = false;
                }
            }

            if ($allBuiltIn) {
                $map[] = new BuiltinProperty($property, $type);
                return;
            }

            $map[] = $this->makeUnionProperty($property, $type);
            return;
        }

        throw new Exception("Unsupported Dto property `" . $property->getName() . "`");
    }

    /**
     * @throws Exception
     */
    private function parseProperty(ReflectionProperty $property, array &$map): void
    {
        if ($property->getAttributes(Ignore::class) || $property->getAttributes(Fetch::class)) {
            return;
        }

        if ($extensions = $property->getAttributes(Extension::class, \ReflectionAttribute::IS_INSTANCEOF)) {
            $extObjects = array_map(fn(\ReflectionAttribute $attribute) => $attribute->newInstance(), $extensions);
            $map[] = new ExtensionProperty($property, $property->getType(), $extObjects);
            return;
        }

        $this->parsePropertyType($property, $property->getType(), $map);
    }

    /**
     * @throws Exception
     */
    public function makeUnionProperty(\ReflectionProperty $property, \ReflectionUnionType $types): UnionProperty
    {
        $typesMap = [];
        foreach ($types->getTypes() as $type) {
            $this->parsePropertyType($property, $type, $typesMap);
        }

        return new UnionProperty($property, $types, $typesMap);
    }

    public function getCollection(): array
    {
        return $this->map;
    }

    /**
     * @return Traversable|DtoProperty[]
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->getCollection());
    }

    /**
     * @throws ReflectionException
     */
    public function newInstance(): Transportable
    {
        return $this->dtoClass->newInstanceWithoutConstructor();
    }
}