<?php

namespace Aspect\Lib\Transport\Blueprint;

use Aspect\Lib\Transport\TransportInterface;

class EnumProperty extends BlueprintProperty
{

    public function __construct(\ReflectionProperty $property, \ReflectionNamedType $type)
    {
        parent::__construct($property, $type);
    }

    public function export(mixed $object, array &$source, TransportInterface $transport): void
    {
        assert($this->type instanceof \ReflectionNamedType);
        $enum = $this->property->getValue($object);
        assert($enum instanceof \UnitEnum);

        if ($enum instanceof \BackedEnum) {
            $source[$this->remoteKey] = $enum->value;
        } else {
            $source[$this->remoteKey] = $enum->value ?? $enum->name;
        }


    }

    public function enrich(array $source, mixed $object, TransportInterface $transport): void
    {
        assert($this->type instanceof \ReflectionNamedType);
        $enumClass = $this->type->getName();
        assert(enum_exists($enumClass));


        $enumValue = $source[$this->remoteKey];

        if (is_a($enumClass, \BackedEnum::class, true)) {
            $this->property->setValue($object, $enumClass::tryFrom($enumValue));
        } else {
            $this->property->setValue($object, constant($enumClass.'::'.$enumValue));
        }
    }
}
