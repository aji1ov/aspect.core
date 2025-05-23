<?php

namespace Aspect\Lib\Transport\Blueprint;

use Aspect\Lib\Transport\Transportable;
use Aspect\Lib\Transport\TransportInterface;

class BuiltinProperty extends BlueprintProperty implements CompatiblePropertyInterface
{

    function export(mixed $object, array &$source, TransportInterface $transport): void
    {
        $value = $this->property->getValue($object);

        if(is_array($value)) {
            $arrayData = [];
            foreach ($value as $key => $slot) {
                if ($slot instanceof Transportable) {
                    $arrayData[$key] = $transport->toArray($slot);
                } else if (is_array($slot)) {
                    $innerMap = [];
                    $this->export($slot, $innerMap, $transport);
                    $arrayData[$key] = $innerMap;
                } else if(is_scalar($slot)) {
                    $arrayData[$key] = $slot;
                } else if(!isset($slot)){
                    continue;
                } else {
                    throw new \Exception("Undefined export value");
                }
            }

            $value = $arrayData;
        }

        $source[$this->remoteKey] = $value;
    }

    function enrich(array $source, mixed $object, TransportInterface $transport): void
    {
        $this->property->setValue($object, $source[$this->remoteKey]);
    }

    public function compatible($source): bool
    {
        assert($this->type instanceof \ReflectionNamedType);
        return match($this->type->getName()) {
            'int' => is_integer($source),
            'bool' => is_bool($source),
            default => gettype($source) === $this->type->getName()
        };
    }
}