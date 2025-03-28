<?php

namespace Aspect\Lib\Transport\Blueprint;

use Aspect\Lib\Transport\DtoBlueprint;
use Aspect\Lib\Transport\TransportInterface;

class DtoProperty extends BlueprintProperty implements CompatiblePropertyInterface
{

    protected string $dtoClass;
    public function __construct(\ReflectionProperty $property, \ReflectionNamedType $type)
    {
        $this->dtoClass = $type->getName();
        parent::__construct($property, $type);
    }

    public function export(mixed $object, array &$source, TransportInterface $transport): void
    {
        $source[$this->remoteKey] = $transport->toArray($this->property->getValue($object));
    }

    public function enrich(array $source, mixed $object, TransportInterface $transport): void
    {
        $this->property->setValue($object, $transport->fromArray($source[$this->remoteKey], $this->dtoClass));
    }

    /**
     * @throws \ReflectionException
     */
    public function compatible($source): bool
    {
        assert(is_array($source));

        $compatible = true;
        $blueprint = new DtoBlueprint($this->dtoClass);

        foreach ($blueprint as $property) {
            if(!$source[$property->remoteKey]) {
                $compatible = false;
                break;
            }

            if ($property instanceof CompatiblePropertyInterface) {
                if (!$property->compatible($source[$property->remoteKey])) {
                   $compatible = false;
                   break;
                }
            }

        }

        return $compatible;
    }
}