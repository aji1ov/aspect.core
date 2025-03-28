<?php

namespace Aspect\Lib\Transport\Blueprint;

use Aspect\Lib\Transport\BlueprintTransport;
use Aspect\Lib\Transport\TransportInterface;

class UnionProperty extends BlueprintProperty
{
    /**
     * @var BlueprintProperty[]
     */
    private array $types;

    public function __construct(\ReflectionProperty $property, \ReflectionUnionType $type, array $types)
    {
        $this->types = $types;
        parent::__construct($property, $type);
    }

    function export(mixed $object, array &$source, TransportInterface $transport): void
    {
        $value = $this->property->getValue($object);
        foreach ($this->types as $type) {
            assert($type->type instanceof \ReflectionNamedType);

            if($type->type->getName() === gettype($value) || is_a($value, $type->type->getName())) {
                $type->export($object, $source, $transport);
                break;
            }
        }
    }

    function enrich(array $source, mixed $object, TransportInterface $transport): void
    {
        $value = $source[$this->remoteKey];
        foreach ($this->types as $type) {
            assert($type->type instanceof \ReflectionNamedType);

            if($type->type->getName() === gettype($value) || is_a($value, $type->type->getName())) {
                $type->enrich($source, $object, $transport);
                break;
            } else if ($type instanceof CompatiblePropertyInterface) {
                if ($type->compatible($value)) {
                    $type->enrich($source, $object, $transport);
                }
            }
        }
    }
}